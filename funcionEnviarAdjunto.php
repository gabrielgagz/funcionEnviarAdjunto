/**
 * @package     funcionEnviarAdjunto
 * @author      Gabriel Gomez (g.a.gomez@gmail.com)
 * @license     https://www.gnu.org/licenses/agpl-3.0.html 
 * 
 * Basado fuertemente en este ejemplo: https://stackoverflow.com/a/12313090
 */
function enviarEmail($mailto,$message,$subject,$dir,$filename,$from,$name)
{

    // Obtenemos los archivos en el directorio
    $dirToScan = scandir($dir);

    // a random hash will be necessary to send mixed content
    $separator = md5(time());

    // carriage return type (RFC)
    $eol = "\r\n";

    // Header (multipart mandatory)
    $headers = "From: $name <$from>" . $eol;
    $headers .= "Reply-To: $name <$from>" . $eol; 
    $headers .= "Return-Path: $name <$from>" . $eol; 
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
    $headers .= "Content-Transfer-Encoding: 7bit" . $eol;
    $headers .= "This is a MIME encoded message." . $eol;
    $headers .= "X-Priority: 3" . $eol;
    $headers .= "X-Mailer: PHP". phpversion() . $eol;
    
    // Cuerpo del Mensaje
    $body = "--" . $separator . $eol;
    $body .= "Content-Type: text/plain; charset=\"iso-8859-1\"" . $eol;
    $body .= "Content-Transfer-Encoding: 8bit" . $eol;
    $body .= $eol . $message . $eol . $eol;

    foreach ($dirToScan as $file) {

        // Chequeamos que lo que estamos adjuntando es un archivo
        if (is_file($dir.$file)):
            // Obtenemos los datos del archivo, y lo insertamos al contenido
            $content = file_get_contents($dir.$file);
            $content = chunk_split(base64_encode($content));

            $body .= "--" . $separator . $eol;
            $body .= "Content-Type: application/octet-stream; name=\"" . $file . "\"" . $eol;
            $body .= "Content-Transfer-Encoding: base64" . $eol;
            $body .= "Content-Disposition: attachment; filename=\"". $file . "\"" . $eol . $eol;
            $body .= $eol . $content . $eol . $eol;

            // Eliminamos el/los adjunto(s)
            unlink ($dir.$file);
        endif; 
    }

    // Cerramos el cuerpo fuera del bucle
    $body .= "--" . $separator . "--";

    // Enviamos el E-Mail
    if (mail($mailto, $subject, $body, $headers)):
        return 0;
    else:
        return -1;
    endif;

}
