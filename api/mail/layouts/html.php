<?php
use yii\helpers\Html;

/** @var \yii\web\View $this view component instance */
/** @var \yii\mail\MessageInterface $message the message being composed */
/** @var string $content main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title>Прораб</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body {
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
            line-height: 100%;
        }

        [style*="Helvetica Neue"] {
            font-family: "Helvetica Neue", Arial, sans-serif !important;
        }

        [style*="Arial"] {
            font-family: Arial, Geneva, Tahoma, sans-serif !important;
        }

        img {
            outline: none;
            text-decoration: none;
            border: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100% !important;
            margin: 0 auto;
            padding: 0;
            display: block;
        }

        .button {
            background-color: #546edb;
            border-radius: 5px;
            color: #ffffff;
            display: inline-block;
            font-family: "Helvetica Neue", Arial;
            font-size: 20px;
            font-weight: bold;
            line-height: 25px;
            text-align: center;
            text-decoration: none;
            width: 335px;
            -webkit-text-size-adjust: none;
            padding: 20px;
            margin-bottom: 20px;
        }

        table td {
            border-collapse: collapse;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        .unsubscribe-mob {
            display: none;
        }

        .px-40 {
            padding-right: 40px;
            padding-left: 40px;
        }

        .py-10 {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .py-20 {
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .pb-20 {
            padding-bottom: 20px;
        }

        .main-title {
            margin: 0;
            font-size: 32px;
            font-style: normal;
            font-weight: 800;
            line-height: 40px;
            text-align: center;
            color: #000000;
        }

        .main-subtitle {
            margin: 0;
            margin-bottom: 13px;
            font-size: 24px;
            font-style: normal;
            font-weight: 700;
            line-height: 30px;
            text-align: center;
            color: #000000;
        }

        .main-text {
            margin: 0;
            text-align: center;
            font-style: normal;
            font-weight: normal;
            font-size: 16px;
            line-height: 20px;
            color: #000000;
        }

        .copyright {
            margin: 0;
            font-size: 10px;
            font-style: normal;
            font-weight: 400;
            line-height: 29px;
            color: #e4ecf9;
            border-top: 1px solid rgba(217, 217, 217, 0.11);
        }

        .unsubscribe-text {
            margin: 0;
            font-size: 10px;
            font-style: normal;
            font-weight: 400;
            line-height: 12px;
            color: #999999;
        }

        .unsubscribe-link {
            margin: 0;
            font-size: 10px;
            font-style: normal;
            font-weight: 400;
            line-height: 12px;
            color: #999999;
        }

        .mobile {
            padding-right: 20px;
            width: 155px;
        }

        .mobile-bottom {
            display: initial;
        }

        .footer-text {
            margin: 0;
            font-size: 15px;
            color: #e4ecf9;
            font-family: "Helvetica Neue", Arial;
        }

        .left,
        .center {
            padding-right: 10px;
        }

        .footer {
            padding: 12px 0;
        }

        @media (max-width: 620px) {
            .table-600 {
                width: 280px !important;
            }

            .logo-600 {
                width: 114px !important;
            }

            .px-40 {
                padding-right: 18px !important;
                padding-left: 18px !important;
            }

            .mobile {
                display: block !important;
                width: 240px;
                padding-right: 0;
            }

            .main-title {
                font-size: 20px;
                font-weight: 700;
                line-height: 23px;
            }

            .main-subtitle {
                font-size: 16px;
                font-weight: 700;
            }

            .main-text {
                margin-top: 20px;
                font-size: 14px;
                line-height: 16px;
            }

            .button {
                font-size: 17px;
                line-height: 20px;
                width: 200px;
                padding: 14px;
            }

            .unsubscribe-desc {
                display: none;
            }

            .unsubscribe-mob {
                text-align: center;
                display: block;
            }

            .footer-text {
                font-size: 14px;
                line-height: 16px;
                padding: 0 12px;
            }

            .copyright {
                font-size: 8px;
                font-weight: 400;
                line-height: 16px;
            }

            .icon {
                display: block;
            }

            .left,
            .center {
                padding: 0;
                padding-bottom: 10px;
            }
        }
    </style>
    <?php $this->head() ?>
</head>
<body style="margin: 0; padding: 0">
    <?php $this->beginBody() ?>
    <?= $content ?>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
