
<div class="items">

    <?php foreach ($cart->getEntries() as $entry): ?>
        <div class="line" data-code="<?=$html($entry->itemName)?>">
            <div class="text">
                <span class="title"><?=$html($entry->itemName)?></span>
                <small class="quantity"><?=$html((int)$entry->quantity)?></small>
                <?php if ($entry->discount->amount->visible > 0): ?>
                    <s class="text-muted pull-right">
                        <small><?=sprintf("%0.2f", $entry->quantity * $entry->pricePerUnit)?></small>
                    </s>
                <?php endif ?>
                </s>
            </div>
            <b class="price pull-left"><?=$html($entry->cash)?></b>
            <span class="discount pull-left">
            </span>
            <form action="?a=removeFromCart" method="POST">
                <input type="hidden" name="item[code]" value="<?=$html($entry->itemCode)?>" />
                <button type="submit" disabled class="remove close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </form>
            <div class="clearfix"></div>
        </div>
    <?php endforeach ?>

</div>
<div class="total">

    <?php if ($cart->cartDiscount->amount->visible > 0): ?>
        <div class="line">
            <div class="text">Discount</div>
            <b class="negative price pull-left"><?=$html($cart->cartDiscount->amount->visible)?></b>
            <div class="clearfix"></div>
        </div>
    <?php endif ?>

    <?php if ($cart->prepaidSpent > 0): ?>
        <div class="line">
            <div class="text">Prepaid</div>
            <b class="price negative pull-left"><?=$html($cart->prepaidSpent)?></b>
            <form action="?a=cancelPrepaidSpend" method="POST">
                <input type="hidden" name="cancel" value="1" />
                <button type="submit" disabled class="remove close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </form>
            <div class="clearfix"></div>
        </div>
    <?php endif ?>

    <div class="line">
        <div class="text">
            Total
            <?php if ($cart->totalDiscount->amount->visible > 0): ?>
                <s class="text-muted pull-right"><small><?=$html($cart->originalAmount->visible)?></small></s>
            <?php endif ?>
        </div>
        <b class="price pull-left"><?=$html($cart->cashAmount->visible)?></b>
        <div class="clearfix"></div>
    </div>
</div>

<?php if ($cart->prepaidReceived && $cart->prepaidReceived->amount->visible > 0): ?>
    <div class="cashback">
        <div class="line">
            <div class="text">Cashback</div>
            <b class="price pull-left"><?=$html($cart->prepaidReceived->amount->visible)?></b>
            <div class="clearfix"></div>
        </div>
    </div>
<?php endif ?>
