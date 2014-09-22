<?php
Mock::generate('OneGoSDK_Impl_APIGateway');
Mock::generate('OneGoSDK_Impl_OAuthTokenBearer');

abstract class BaseOneGoAPITest extends BaseUnitTestCase
{
    /**
     * @var OneGoSDK_Interface_OneGoAPI
     */
    protected $unit;

    /**
     * @var OneGoSDK_Interface_Gateway
     */
    protected $gateway;
    
    protected $token;

    public function setUp()
    {
        $this->gateway = new MockOneGoSDK_Impl_APIGateway($this->getApiConfig());
        $this->unit = new OneGoSDK_Impl_OneGoAPI($this->gateway);
        $this->token = new MockOneGoSDK_Impl_OAuthTokenBearer();
        $this->unit->setOAuthToken($this->token);
    }
    
    protected function newMinTransactionResponse()
    {
        $tx = new OneGoSDK_DTO_TransactionDto();
        $tx->id = $this->newTransactionId();
        $tx->terminalId = $this->getApiConfig()->terminalId;
        $tx->receiptNumber = 'rcpt123';
        return $tx;
    }
    
    protected function newMinTransactionBeginRequest()
    {
        $request = new OneGoSDK_DTO_TransactionBeginRequestDto();
        $request->terminalId = 'T1';
        $request->receiptNumber = 'no1234';
        $request->cartEntries = null;
        return $request;
    }
    
    protected function newTransactionId()
    {
        $dto = new OneGoSDK_DTO_TransactionIdDto();
        $dto->id = '111';
        $dto->type = 'ONEGO';
        return $dto;
    }
    
    protected function newTransactionEndRequest($status)
    {
        $req = new OneGoSDK_DTO_TransactionEndDto();
        $req->id = $this->newTransactionId();
        $req->status = $status;
        $req->transactionId = 'tx123456';
        return $req;
    }
    
    protected function newUpdateCartRequest()
    {
        $req = new OneGoSDK_DTO_TransactionCartDto();
        $req->transactionId = $this->newTransactionId();
        $items = array();
        $items[] = $this->newCartItem(1, 111, 20);
        $items[] = $this->newCartItem(2, 112, 7.5, 3);
        $req->cartEntries = $items;
        return $req;
    }
    
    protected function newFundsSpendingRequest()
    {
        $req = new OneGoSDK_DTO_FundsOperationDto();
        $req->transactionId = $this->newTransactionId();
        $req->amount = 123.56;
        return $req;
    }
    
    /**
     * @return OneGoSDK_DTO_CartItemDto
     */
    protected function newCartItem($index, $code, $price, $quantity = 1)
    {
        $item = new OneGoSDK_DTO_CartEntryDto();
        $item->index = $index;
        $item->itemCode = $code;
        $item->pricePerUnitDto = new OneGoSDK_DTO_AmountDto((string) $price);
        $item->quantity = $quantity;
        $item->cashAmountDto = new OneGoSDK_DTO_AmountDto((string) ($price * $quantity));

        return $item;
    }
    
    protected function newCart($itemsCount)
    {
        $items = array();
        for ($i = 1; $i < $itemsCount; $i++) {
            $items[] = $this->newCartItem($i, 'itm'.$i, rand(100, 800) / 10, rand(1, 10));
        }
        return $items;
    }
    
    protected function newModifiedCart($items)
    {
        $dto = new OneGoSDK_DTO_ModifiedCartDto();
        $dto->entries = $items; // just for count verification
        return $dto;
    }
}