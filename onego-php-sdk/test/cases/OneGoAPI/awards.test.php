<?php
class AwardsTest extends BaseOneGoAPITest
{
    public function testAwards()
    {
        $req = new OneGoSDK_DTO_CalculateAwardsDto();
        $req->terminalId = $this->getApiConfig()->terminalId;
        $req->cartEntries = $this->newCart(10);
        
        $this->gateway->expect('awards', array($req));
        $this->gateway->setReturnValue(
            'awards',
            $this->newModifiedCart($req->cartEntries),
            array($req)
        );

        $response = $this->unit->awards($req);

        $this->assertIsA($response, 'OneGoSDK_DTO_ModifiedCartDto');
        $this->assertEqual(count($req->cartEntries), count($response->entries));
    }
}