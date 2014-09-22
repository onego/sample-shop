<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

Mock::generate('OneGoSDK_Impl_OneGoAPI');
Mock::generate('OneGoSDK_Impl_APIGateway');
Mock::generate('OneGoSDK_Interface_HttpClient');
Mock::generate('OneGoSDK_Interface_OAuthToken');

abstract class BaseSimpleAPITest extends BaseUnitTestCase
{
    /**
     * @var OneGoSDK_Interface_OneGoAPI
     */
    protected $unit;

    /**
     * @var OneGoSDK_Interface_OneGoAPI
     */
    protected $api;
    
    protected $gateway;

    /**
     * @var string
     */
    protected $token;

    public function setUp()
    {
        $this->gateway        = new MockOneGoSDK_Impl_APIGateway(
            $this->getApiConfig(),
            new MockOneGoSDK_Interface_HttpClient()
        );

        $this->api      = new MockOneGoSDK_Impl_OneGoAPI($this->gateway);
        $this->api->setReturnValue('getConfig', $this->getApiConfig());

        $this->unit     = new OneGoSDK_Impl_SimpleAPI($this->api);

        $this->token    = new MockOneGoSDK_Interface_OAuthToken();
        
    }
  
    protected function newTransactionBeginRequest()
    {
        $req = new OneGoSDK_DTO_TransactionBeginRequestDto();
        $req->terminalId = $this->getApiConfig()->terminalId;
        $req->receiptNumber = 'rcpt321';
        $req->ttl = $this->getApiConfig()->transactionTtl;
        $req->ttlAutoRenew = $this->getApiConfig()->transactionTtlAutoRenew;
        return $req;
    }
    
    /**
     *
     * @param OneGoSDK_DTO_TransactionBeginRequestDto $req
     * @return OneGoSDK_DTO_TransactionDto 
     */
    protected function newTransactionMin(OneGoSDK_DTO_TransactionBeginRequestDto $req)
    {
        $dto = new OneGoSDK_DTO_TransactionDto();
        $dto->terminalId = $req->terminalId;
        $dto->id = new OneGoSDK_DTO_TransactionIdDto();
        $dto->id->id = 'ID123';
        $dto->id->type = 'ONEGO';
        $dto->receiptNumber = $req->receiptNumber;
        return $dto;
    }
    
    protected function newTransactionFull(OneGoSDK_DTO_TransactionBeginRequestDto $req)
    {
        $dto = $this->newTransactionMin($req);
        $dto->externalId = new OneGoSDK_DTO_TransactionIdDto();
        $dto->externalId->id = 'EXTID123';
        $dto->externalId->type = 'EXTERNAL';
        $dto->screenMessage = 'screen message';
        $dto->buyerInfo = new OneGoSDK_DTO_BuyerInfoDto();
        $dto->buyerInfo->prepaidAvailable = 123.45;
        $dto->modifiedCart = new OneGoSDK_DTO_ModifiedCartDto();
        $dto->modifiedCart->originalAmount = OneGoSDK_DTO_AmountDto::asCash(345.6789);
        $dto->modifiedCart->cashAmount = OneGoSDK_DTO_AmountDto::asCash(234.5678);
        $dto->modifiedCart->payableAmount = OneGoSDK_DTO_AmountDto::asCash(222.2222);
        $dto->modifiedCart->entryDiscount = new OneGoSDK_DTO_DiscountDto();
        $dto->modifiedCart->entryDiscount->amount = OneGoSDK_DTO_AmountDto::asCash(11.1111);
        $dto->modifiedCart->entryDiscount->percent = 5.55;
        $dto->modifiedCart->cartDiscount = new OneGoSDK_DTO_DiscountDto();
        $dto->modifiedCart->cartDiscount->amount = OneGoSDK_DTO_AmountDto::asCash(15.1515);
        $dto->modifiedCart->cartDiscount->percent = 6.66;
        $dto->modifiedCart->totalDiscount = new OneGoSDK_DTO_DiscountDto();
        $dto->modifiedCart->totalDiscount->amount = OneGoSDK_DTO_AmountDto::asCash(18.1818);
        $dto->modifiedCart->totalDiscount->percent = 7.77;
        $dto->modifiedCart->prepaidSpent = 23.45;
        $dto->modifiedCart->prepaidTopup = 45.67;
        $dto->modifiedCart->prepaidReceived = new OneGoSDK_DTO_FundsReceivedDto();
        $dto->modifiedCart->prepaidReceived->amount = OneGoSDK_DTO_AmountDto::asCash(66.6666);
        return $dto;
    }
    
    protected function beginTransaction(OneGoSDK_DTO_TransactionBeginRequestDto $requestDto)
    {
        $this->api->setReturnValue('beginTransaction', $this->newTransactionFull($requestDto));
        $transaction = $this->unit->beginTransaction($requestDto->receiptNumber);
        return $transaction;
    }
}