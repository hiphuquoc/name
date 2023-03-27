<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Services\BuildInsertUpdateModel;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderProduct;

class OrderController extends Controller{

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function create(Request $request){
        try {
            DB::beginTransaction();
            /* kiểm tra customer tồn tại chưa => chưa thì tạo customer_info */
            $infoCustomer       = Customer::select('*')
                                    ->where('phone', $request->get('phone'))
                                    ->first();
            if(!empty($infoCustomer)){
                $idCustomer     = $infoCustomer->id;
            }else {
                $insertCustomer = $this->BuildInsertUpdateModel->buildArrayTableCustomerInfo($request->all());
                $idCustomer     = Customer::insertItem($insertCustomer);
            }
            /* kiểm tra customer_address => chưa có thêm vào */
            $customerAddress    = CustomerAddress::select('*')
                                    ->where('customer_info_id', $idCustomer)
                                    ->get();
            $flagExists         = false;
            foreach($customerAddress as $address){
                if($address->address==$request->get('address')&&$address->province_info_id==$request->get('province_info_id')&&$address->district_info_id==$request->get('district_info_id')){
                    $flagExists = true;
                }
            }
            if($flagExists==false){
                $insertCustomerAddress = $this->BuildInsertUpdateModel->buildArrayTableCustomerAddress($request->all(), $idCustomer);
                CustomerAddress::insert($insertCustomerAddress);
            }
            /* tạo order_info */
            $products               = \App\Http\Controllers\CartController::getCollectionProducts();
            $insertOrderInfo        = $this->BuildInsertUpdateModel->buildArrayTableOrderInfo($request->all(), $idCustomer, $products);
            $idOrderInfo            = Order::insertItem($insertOrderInfo);
            /* tạo order_product */
            foreach($products as $product){
                $insertProductPrice = [
                    'order_info_id'     => $idOrderInfo,
                    'product_info_id'   => $product->id,
                    'product_price_id'  => $product->price->id,
                    'quantity'          => $product->cart['quantity'],
                    'price'             => $product->price->price
                ];
                OrderProduct::insert($insertProductPrice);
            }
            /* Xóa cookie */
            \App\Http\Controllers\CartController::removeCookie('cart');
            DB::commit();
            return redirect()->route('main.viewConfirm', ['code' => $insertOrderInfo['code']]);
        } catch (\Exception $exception){
            DB::rollBack();
            /* chuyển sang trang thông báo lỗi */
        }
        
    }

    public function viewConfirm(Request $request){
        if(!empty($request->get('code'))){
            $order          = Order::select('*')
                                ->where('code', $request->get('code'))
                                ->with('products.infoProduct', 'products.infoPrice', 'paymentMethod')
                                ->first();
            return view('main.order.confirm', ['order' => $order, 'action' => true]);
        }
    }

}
