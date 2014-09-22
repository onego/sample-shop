<?php

final class OneGoSDK_DTO_TransactionDto
{
    public $id = true;

    public $externalId = null;
    
    public $terminalId = true;
    
    public $screenMessage = null;
    
    public $modifiedCart = null;

    public $buyerInfo = null;
    
    public $receiptNumber = true;
    
    public $redemptionCode = null;
    
    public $expiresIn = true;
    
    /**
     *
     * @return OneGoSDK_DTO_TransactionIdDto 
     */
    public function getId()
    {
        return OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_TransactionIdDto', $this->id);
    }
    
    /**
     *
     * @return OneGoSDK_DTO_TransactionIdDto 
     */    
    public function getExternalId()
    {
        if (!empty($this->externalId)) {
            return OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_TransactionIdDto', $this->externalId);
        }
        return null;
    }
    
    /**
     *
     * @return OneGoSDK_DTO_ModifiedCartDto 
     */
    public function getModifiedCart()
    {
        if (!empty($this->modifiedCart)) {
            return OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_ModifiedCartDto', $this->modifiedCart);
        }
        return null;
    }
    
    /**
     *
     * @return OneGoSDK_DTO_BuyerInfoDto 
     */
    public function getBuyerInfo()
    {
        if (!empty($this->buyerInfo)) {
            return OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_BuyerInfoDto', $this->buyerInfo);
        }
        return null;
    }
    
    /**
     *
     * @return OneGoSDK_DTO_RedemptionCodeDto
     */
    public function getRedemptionCode()
    {
        if (!empty($this->redemptionCode)) {
            return OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_RedemptionCodeDto', $this->redemptionCode);
        }
        return null;
    }
}
