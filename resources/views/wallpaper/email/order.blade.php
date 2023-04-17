{{-- <div>Đây là email test</div>
@foreach($order->products as $product)
    @php
        $zipPath = $product->infoPrice->folder_drive.'-'.$product->infoProduct->seo->slug.'/'.$product->infoPrice->folder_drive.'-'.$product->infoProduct->seo->slug.'.zip';
    @endphp
    <a href="{{ Storage::disk('google')->url($zipPath) }}" target="_blank">
        <div>{{ $product->infoProduct->name }} (link google drive .ZIP)</div>
    </a>
@endforeach --}}

<table class="sendEmail" style="font-family:'Roboto',Montserrat,-apple-system,'Segoe UI',sans-serif;width:100%;background-color:#fff;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tbody>
        <tr>
            <td align="center" style="font-family:'Roboto',Montserrat,-apple-system,'Segoe UI',sans-serif;">
                <table class="sendEmail" role="presentation" style="border-collapse:collapse;background:#1a202c;border-radius:3px;width:100%;max-width:640px;margin:20px auto 40px auto;">
                    <tbody>
                        
                        <tr>
                            <td style="box-sizing:border-box;">
                                <div style="text-align:center">
                                    <img src="https://name.com.vn/storage/images/upload/image-email-type-manager-upload.webp" style="display:inline-block;width:100%;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="box-sizing:border-box;font-weight:bold;padding:10px 15px 10px 15px;background:#2d3848;">
                                <div style="font-size:14px;font-weight:normal;color:#c0d0f199;">Nền tảng <a href="https://name.com.vn" style="color:#f7ff93;">Name.com.vn</a> thuộc CÔNG TY TNHH DU LỊCH BIỂN ĐẢO HITOUR - MST: 1702204052</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="display:none;">
                                @foreach($order->products as $product)
                                    @php
                                        $zipPath = $product->infoPrice->folder_drive.'-'.$product->infoProduct->seo->slug.'/'.$product->infoPrice->folder_drive.'-'.$product->infoProduct->seo->slug.'.zip';
                                    @endphp
                                    <a href="{{ Storage::disk('google')->url($zipPath) }}" target="_blank">
                                        <div>{{ $product->infoProduct->name }} (link google drive .ZIP)</div>
                                    </a>
                                @endforeach
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>