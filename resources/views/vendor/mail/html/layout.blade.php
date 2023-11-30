<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
</head>

<body>
<style>
    .inner-body {
        width: 60% !important;
        padding: 30px !important;
        padding-bottom: 0px !important;
        background: #f1f6fe !important;
    }

    .ml-110 {
        margin-left: 110px;
    }


    @media only screen and (max-width: 600px) {
        .inner-body {
            width: 100% !important;
        }

        .footer {
            width: 100% !important;
        }
    }

    @media only screen and (max-width: 500px) {
        .button {
            width: 100% !important;
        }

        .ml-110 {
            margin-left: 0;
        }
    }

</style>
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
       role="presentation">
    <!-- Body content -->
    {{ $header ?? '' }}
    <tr style="background: #e6eefd">
        <td class="content-cell">
            <div class="ml-110">
                {{ Illuminate\Mail\Markdown::parse($slot) }}
            </div>
        </td>
    </tr>
    <tr style="background: #ffffff">
        <td class="content-cell">
            <div class="ml-110">
                {{ $subcopy ?? '' }}
            </div>
        </td>
    </tr>
    <tr style="background: #ffffff">
        <td class="content-cell">
            <div class="ml-110" style="border-top: 1.5px solid #1f1f1f; padding-top: 15px">
                {{ $social ?? '' }}
            </div>
        </td>
    </tr>
    {{ $footer ?? '' }}
</table>
</body>

</html>
