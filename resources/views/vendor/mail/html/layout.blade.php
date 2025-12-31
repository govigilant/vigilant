<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
@import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap');

:root {
    color-scheme: dark;
    supported-color-schemes: dark;
}

body {
    margin: 0;
    background-color: #0A0A0F;
    color: #C8C8DC;
    font-family: 'Figtree', 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
}

table {
    border-collapse: collapse;
}

a {
    color: #60A5FA;
}

.wrapper {
    background-color: #0A0A0F;
    padding: 0 16px 40px;
    width: 100%;
}

.content {
    width: 100%;
}

.header {
    padding: 48px 0 12px;
    background-color: #0A0A0F;
}

.body {
    width: 100%;
    background-color: #0A0A0F;
}

.inner-body {
    width: 600px;
    background-color: #1A1A24;
    border-radius: 28px;
    box-shadow: 0 35px 65px rgba(5, 5, 10, 0.65);
    overflow: hidden;
}

.content-cell {
    padding: 48px 48px 40px;
    color: #C8C8DC;
    font-size: 16px;
    line-height: 1.7;
}

.content-cell h1,
.content-cell h2,
.content-cell h3 {
    color: #FAFAFF;
    font-weight: 600;
    margin-top: 0;
}

.content-cell h1 {
    font-size: 30px;
    margin-bottom: 18px;
}

.content-cell h2 {
    font-size: 24px;
    margin-bottom: 14px;
}

.content-cell h3 {
    font-size: 20px;
    margin-bottom: 12px;
}

.content-cell p,
.content-cell ul,
.content-cell ol {
    margin: 0 0 16px;
    color: #C8C8DC;
}

.content-cell strong {
    color: #F4F4FA;
}

.content-cell hr {
    border: 0;
    border-top: 1px solid #2D2D42;
    margin: 32px 0;
}

.panel {
    margin: 32px 0;
    border-radius: 20px;
    border: 1px solid #2D2D42;
    background-color: #232333;
}

.panel-content {
    padding: 0 32px 32px;
}

.panel-item {
    color: #C8C8DC;
}

.table table {
    width: 100% !important;
    background-color: #232333;
    border-radius: 18px;
    border: 1px solid #2D2D42;
}

.subcopy {
    margin-top: 32px;
    border-radius: 20px;
    background-color: #232333;
    border: 1px solid #2D2D42;
}

.subcopy td {
    padding: 20px 24px;
    color: #A8A8C0;
    font-size: 13px;
}

.footer {
    background-color: #0A0A0F;
    border-top: 1px solid #2D2D42;
}

.footer .content-cell {
    color: #7A7A94;
    font-size: 13px;
    padding: 32px 16px;
    letter-spacing: 0.01em;
}

.action {
    width: 100%;
    margin: 36px 0;
}

.button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 14px 34px;
    font-weight: 600;
    font-size: 15px;
    text-decoration: none;
    border-radius: 999px;
    color: #FAFAFF !important;
    background-color: #EF4444;
    background-image: linear-gradient(120deg, #EF4444 0%, #F97316 50%, #EF4444 100%);
    border: 0;
    letter-spacing: 0.02em;
}

.button-secondary {
    background-color: transparent;
    background-image: none;
    color: #F4F4FA !important;
    border: 1px solid #444459;
    box-shadow: none;
}

.button-success {
    background-color: #10B981;
    background-image: linear-gradient(120deg, #10B981 0%, #34D399 100%);
}

.button-error {
    background-color: #DC2626;
    background-image: linear-gradient(120deg, #DC2626 0%, #EF4444 100%);
}

@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}

.content-cell {
padding: 32px 24px;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}
</style>
</head>
<body style="margin:0;padding:0;background-color:#0A0A0F;color:#C8C8DC;">

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#0A0A0F;">
<tr>
<td align="center" style="background-color:#0A0A0F;">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;background-color:#0A0A0F;">
{{ $header ?? '' }}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;background-color:#0A0A0F;padding:24px 0 48px;">
<table class="inner-body" align="center" width="600" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#1A1A24;border-radius:28px;box-shadow:0 35px 65px rgba(5,5,10,0.65);overflow:hidden;">
<!-- Body content -->
<tr>
<td class="content-cell" style="padding:48px 48px 40px;color:#C8C8DC;font-size:16px;line-height:1.7;">
{{ Illuminate\Mail\Markdown::parse($slot) }}

{{ $subcopy ?? '' }}
</td>
</tr>
</table>
</td>
</tr>

{{ $footer ?? '' }}
</table>
</td>
</tr>
</table>
</body>
</html>
