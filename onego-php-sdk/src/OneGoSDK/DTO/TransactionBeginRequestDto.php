<?php

final class OneGoSDK_DTO_TransactionBeginRequestDto
{
    public $terminalId;

    public $receiptNumber;
    
    public $bimId;

    public $time;

    public $externalId;

    public $cartEntries;
    
    public $ttl;
    
    public $ttlAutoRenew;
}
