<?php

final class OneGoSDK_DTO_TransactionEndDto
{
    const STATUS_CANCEL     = 'CANCEL';
    const STATUS_CONFIRM    = 'CONFIRM';
    const STATUS_DELAY    = 'DELAY';
    
    public $transactionId;
    public $status;
    public $ttl;
    
    public static function getStatusesAvailable()
    {
        return array(self::STATUS_CANCEL, self::STATUS_CONFIRM, self::STATUS_DELAY);
    }
}
