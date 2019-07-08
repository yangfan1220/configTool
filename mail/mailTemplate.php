<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-07-08
 * Time: 17:35
 */
/**
 * @params $message
 */

?>
<body style="margin: 0; padding: 0;">

<table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">

    　
    <tr>
        <td>
            <div style="margin: 20px;text-align: center;margin-top: 50px">
                <img src="<?= $message->embed($imageFileName); ?>" border="0" style="display:block;width: 100%;height: 100%">
            </div>
        </td>
    </tr>

    <tr>
        <td>
            <div style="border: #36649d 1px dashed;margin: 30px;padding: 20px">
                <label style="font-size: 22px;color: red;font-weight: bold">异常</label>
                <?php
                foreach ($errorMessages as $errorMessage) {
                    echo ' <p style="font-size: 16px">'.$errorMessage.'</p>';
                }
                ?>
            </div>
        </td>
    </tr>
    　
    <tr>
        <td>
            <div style="margin: 40px">
                <p style="font-size: 16px"></p>
                <p style="color:red;font-size: 14px ">（这是一封自动发送的邮件，请勿回复。）</p>

            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div align="right" style="margin: 40px;border-top: solid 1px gray" id="bottomTime">
                <p style="margin-right: 20px"></p>
                <label style="margin-right: 20px"><?=date('Y年m月d日H时i分s秒')?></label>
            </div>
        </td>
    </tr>
</table>
</body>

