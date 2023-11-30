<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <style>
            * {
                margin: 0;
            }
            .container {
                margin: 5rem auto;
            }
            .text-center {
                text-align: center;
            }
            .p-1{
                padding: 1rem;
            }
            .bg-dark {
                background-color: #000 !important;
            } 
            .text-white {
                color: #fff !important;
            }
            .fw-bold.fs-1.text-center{
                margin-top: 1rem;
                font-size: 1.5rem;
                font-weight: 700;
            }
            .card {
                margin-top: 1rem;
                border: 0;
                border-radius: 0.5rem;
                box-shadow: 0 0 0.5rem rgba(0, 0, 0, 0.1);
            }
            .card-img-top.img-fluid{
                border-radius: 0.5rem 0.5rem 0 0;
                width: 100%;
                height: 100px;
                margin-bottom: 1rem;
            }
            .card-body{
                padding: 1rem;
            }
            .card-img-top.img-fluid{
                object-fit: cover;
                height: 300px;
            }
            .profile img{
                width: 9rem;
                height: 9rem;
                border-radius: 50%;
                margin: 1rem auto;
                object-fit: cover;
            }
            .card-title {
                font-size: 2rem;
                font-weight: 700;
                margin-top: .1rem;
                margin-bottom: .1rem;
            }
            .card-text {
                color: rgb(78, 78, 78);
                font: 1.6em sans-serif;
                margin-top: .5rem;
                margin-bottom: .5rem;
            }
            .btn.btn-primary.p-2.fs-4.my-3 {
                background-color: #0068E1;
                color: #fff;
                border: 0;
                border-radius: 0.3rem;
                padding: .5rem 1rem;
                font-size: 1.5rem;
            }
        </style>
    </head>
    <body class="h-100 container">
        {{ Illuminate\Mail\Markdown::parse($slot) }}
    </body>
</html>
