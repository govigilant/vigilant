<table class="panel" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:32px 0;border-radius:24px;border:1px solid #3A3A56;background-color:#151524;box-shadow:0 18px 40px rgba(3,3,15,.55);overflow:hidden;">
<tr>
<td style="height:8px;background:linear-gradient(120deg,#6C63FF,#61E1FF,#4ECDC4);padding:0;"></td>
</tr>
<tr>
<td class="panel-content" style="padding:32px;background:linear-gradient(180deg,rgba(34,34,58,0.98),rgba(16,16,34,0.98));">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="panel-item" style="color:#F4F4FF;font-size:15px;line-height:1.7;">
{{ Illuminate\Mail\Markdown::parse($slot) }}
</td>
</tr>
</table>
</td>
</tr>
</table>

