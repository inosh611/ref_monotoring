<?php

namespace Modules\Dealers\Repositories;

use App\Traits\ApiCrudTrait;
use Illuminate\Support\Facades\Auth;
use Modules\Dealers\Entities\ShopStock;
use Modules\Dealers\Repositories\Interfaces\DealerStockRepositoryInterface;


class DealerStockRepository implements DealerStockRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(ShopStock $dealer)
    {
        $this->model = $dealer;
    }

    public function allData()
    {
        return $this->model->query();
    }
    public function find($orderId, $shopId)
    {
        return $this->model->where('order_id', $orderId)->where('shop_id', $shopId)->with('item', 'order', 'item.product', 'item.product.unit')->get();
    }
    public function update(array $data)
    {

        foreach ($data as $item) {
            $model = $this->model->findOrFail($item->id);

            // cast $item to array
            $itemArray = (array) $item;

            if (!empty($itemArray['quantity'])) {
                $itemArray['user_id'] = Auth::user()->id;
                $model->update($itemArray);
            }
        }

        return $model;
    }
}
