<?php

namespace Shopen\Http\Controllers\Admin\Api;


class InvoiceItemsController
{
    public function recalculate()
    {
        $data = request('item');

        $netPrice = floatval(str_replace(',', '.', $data['price_net']));
        $taxRate = floatval($data['tax_rate']);
        $discountNet = floatval(str_replace(',', '.', $data['discount_amount_net']));
        $qty = $data['quantity'];

        $price = round($netPrice * (1 + $taxRate / 100), 2);
        $discount = round($discountNet * (1 + $taxRate / 100), 2);

        $totalNet = round(($netPrice - $discountNet) * $qty, 2);
        $total = round(($price - $discount) * $qty, 2);

        $tax = $totalNet * $taxRate / 100;

        return [
            'price_net' => $netPrice,
            'total' => $total,
            'total_net' => $totalNet,
            'tax_amount' => $tax,
        ];
    }
}