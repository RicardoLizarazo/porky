<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderDetail;

class MigrateOrders extends Command
{
    protected $signature = 'migrate:orders';
    protected $description = 'Migrar pedidos';

    // 🔹 Normalizar teléfono
    private function normalizePhone($phone)
    {
        if (!$phone) return null;

        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) > 10) {
            if (str_starts_with($phone, '57')) {
                $phone = substr($phone, 2);
            } elseif (str_starts_with($phone, '0')) {
                $phone = substr($phone, 1);
            }
        }

        return $phone;
    }

    // 🔹 Normalizar nombre
    private function normalizeName($name)
    {
        if (!$name) return null;

        $name = strtolower(trim($name));

        return str_replace(
            ['á','é','í','ó','ú','ñ'],
            ['a','e','i','o','u','n'],
            $name
        );
    }

    public function handle()
    {
        DB::beginTransaction();

        try {

            $map = [];
            $missingCustomers = 0;
            $missingProducts = 0;

            // 🔹 Cache clientes con normalización previa
            $customers = DB::table('customers')->get()->map(function ($c) {

                $phone = preg_replace('/\D/', '', $c->telephone);

                if (strlen($phone) > 10 && str_starts_with($phone, '57')) {
                    $phone = substr($phone, 2);
                }

                $name = strtolower(trim($c->name));
                $name = str_replace(
                    ['á','é','í','ó','ú','ñ'],
                    ['a','e','i','o','u','n'],
                    $name
                );

                $c->phone_normalized = $phone;
                $c->name_normalized  = $name;

                return $c;
            });

            // 🔹 Migrar orders
            $oldOrders = DB::table('orders_old')->get();

            foreach ($oldOrders as $old) {

                $customerOld = DB::table('users_old')
                    ->where('id', $old->id_customer)
                    ->first();

                if (!$customerOld) {
                    $this->warn("Cliente OLD no encontrado ID: {$old->id_customer}");
                    $missingCustomers++;
                    continue;
                }

                $phoneOld = $this->normalizePhone($customerOld->telephone);
                $nameOld  = $this->normalizeName($customerOld->name);

                $customer = null;

                // 🔹 1. MATCH POR TELÉFONO (PRINCIPAL)
                if ($phoneOld) {
                    $customer = $customers->firstWhere('phone_normalized', $phoneOld);
                }

                // 🔹 2. MATCH POR NOMBRE EXACTO
                if (!$customer && $nameOld) {
                    $customer = $customers->firstWhere('name_normalized', $nameOld);
                }

                if (!$customer) {
                    $this->warn("Cliente no encontrado: {$customerOld->name} - {$customerOld->telephone}");
                    $missingCustomers++;
                    continue;
                }

                $new = Order::create([
                    'customer_id'      => $customer->id,
                    'user_id'          => null,
                    'delivery_user_id' => null,
                    'type_id'          => 1,
                    'status_id'        => 5,
                    'payment_method'   => $old->payment,
                    'total_items'      => $old->total_items,
                    'subtotal'         => $old->subtotal,
                    'total'            => $old->total,
                    'delivery_cost'    => 0,
                    'packaging_total'  => 0,
                    'indication'       => $old->indication,
                    'comment'          => $old->comment,
                    'ordered_at'       => $old->date,
                ]);

                $map[$old->id] = $new->id;
            }

            // 🔹 Migrar detalles
            $oldDetails = DB::table('detail_orders_old')->get();

            foreach ($oldDetails as $old) {

                if (!isset($map[$old->id_order])) continue;

                $productOld = DB::table('products_old')
                    ->where('id', $old->id_product)
                    ->first();

                if (!$productOld) continue;

                $productName = strtolower(trim($productOld->name));

                // 🚚 Domicilio
                if ($productName === 'domicilio') {
                    Order::where('id', $map[$old->id_order])
                        ->update(['delivery_cost' => $old->subtotal]);
                    continue;
                }

                // 📦 Empaque
                if ($productName === 'icopor') {
                    Order::where('id', $map[$old->id_order])
                        ->update([
                            'packaging_total' => DB::raw("packaging_total + {$old->subtotal}")
                        ]);
                    continue;
                }

                $product = DB::table('products')
                    ->where('code', (int) $productOld->code)
                    ->first();

                if (!$product) {
                    $missingProducts++;
                    $this->warn("Producto no encontrado: {$productOld->name}");
                    continue;
                }

                OrderDetail::create([
                    'order_id'     => $map[$old->id_order],
                    'product_id'   => $product->id,
                    'product_name' => $productOld->name,
                    'quantity'     => $old->quantity,
                    'price'        => $old->price,
                    'subtotal'     => $old->subtotal,
                    'comment'      => $old->comment,
                ]);
            }

            DB::commit();

            $this->info('✅ Migración completada correctamente');
            $this->warn("⚠️ Clientes no encontrados: {$missingCustomers}");
            $this->warn("⚠️ Productos no encontrados: {$missingProducts}");

        } catch (\Exception $e) {

            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}