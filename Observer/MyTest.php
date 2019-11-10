<?php

namespace AN\QtyEvent\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;


class MyTest implements ObserverInterface
{


	protected $_logger;

  protected $_productFactory;

  protected $_getSalableQuantityDataBySku;
  



  public function __construct
  (
    \Psr\Log\LoggerInterface $logger,
    ProductFactory $productFactory,
    GetSalableQuantityDataBySku $getSalableQuantityDataBySku
  )
  {
   $this->_logger = $logger;
   $this->_productFactory=$productFactory;
   $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
 }


 public function getQtyStock($id)
 { 

  $product=$this->_productFactory->create()->load($id);

  $sku = $product->getSku();
  $salable = $this->getSalableQuantityDataBySku->execute($sku);
  return $salable[0]['qty'];
}

public function execute(\Magento\Framework\Event\Observer $observer) {

     //order id
 $order = $observer->getEvent()->getOrder();
 $order_id = $order->getIncrementId();
 $this->_logger->info($order_id);

     //product id list
 foreach($order->getAllItems() as $item)
 {
   $ProdustIds[]= $item->getProductId();
   $productQty[] = $item->getQtyToInvoice();
 }

 foreach($productQty as $qty)
 {
     $getQty=$qty;
 }

 foreach($ProdustIds as $id)
 {
     // if(getQtyStock($id)==0)
     // {
     //  $product=$this->_productFactory->create()->load($id);
     //  $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
     //  $product->save();
     // }
   $product=$this->_productFactory->create()->load($id)->getStoreId(0);
   // $product->setName('ken97');
   // $product->save();

   $sku = $product->getSku();
   $salable = $this->getSalableQuantityDataBySku->execute($sku);
   $salableQty=$salable[0]['qty'];
   $this->_logger->info($salableQty);

   if(($salableQty-$getQty)==0)
   {
    $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED)->save();
    // $product->setQty(0);
    // $product->save();
   }

 }
}


}




