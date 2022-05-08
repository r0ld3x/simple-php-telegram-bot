<?php

date_default_timezone_set('America/Lima');

#FUNCION PARA ENVIAR MENSAJES
    
    function EnviarMensaje($chatId, $message, $message_id)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org:443/bot'.$GLOBALS['botToken'].'/sendMessage');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'HTTP/1.1 200 OK'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"chat_id":"'.$chatId.'","text":"'.$message.'","reply_to_message_id":"'.$message_id.'","parse_mode":"HTML"}');
        $result = curl_exec($ch);
    }

#FUNCION PARA AÑADIR CHAT-IDs(USUARIOS O GRUPOS)
    
    function VerificarAdmin($userId)
    {
        $file = array_values(array_unique(file('Users/Admins.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
        foreach ($file as $key => $value) {
            if ($value == $userId) {
                $Admin = true;
                $GLOBALS['Admin'] = $Admin;
                break;
            } else {
                $Admin = false;
                $GLOBALS['Admin'] = $Admin;
            }
        }
    }

#FUNCION PARA VERIFICAR PREMIUM
    
    function VerificarPremium($Time)
    {
        global $chatId, $username, $userId, $message_id, $My_ID, $Admin;
        $TiempoActual = time();
        if ($TiempoActual > $Time) {
            $file = array_values(array_unique(file('Users/Premium.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
            $out = [];
            foreach ($file as $line) {
                $user_value = explode("|", $line)[0];
                if ($user_value == $userId) {
                    $delete[] = $line;
                } else {
                    $out[] = $line;
                }
            }
            $fp = fopen('./Users/Premium.txt', "w+");
            foreach ($out as $line) {
                fwrite($fp, $line . PHP_EOL);
            }
            fclose($fp);
            $Rank = 'USER';
            $GLOBALS['Rank'] = $Rank;
        }
    }

#FUNCION PARA VERIFICAR PREMIUM DEL GRUPO
    
    function VerificarPremiumGrupo($Time)
    {
        global $chatId, $username, $userId, $message_id, $My_ID, $Admin;
        $TiempoActual = time();
        if ($TiempoActual > $Time) {
            $file = array_values(array_unique(file('Users/Premium.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
            $out = [];
            foreach ($file as $line) {
                $user_value = explode("|", $line)[0];
                if ($user_value == $chatId) {
                    $delete[] = $line;
                } else {
                    $out[] = $line;
                }
            }
            $fp = fopen('./Users/Premium.txt', "w+");
            foreach ($out as $line) {
                fwrite($fp, $line . PHP_EOL);
            }
            fclose($fp);
            $Rank_Group = 'USER';
            $GLOBALS['Rank_Group'] = $Rank_Group;
        }
    }

#FUNCION PARA AÑADIR CHAT-IDs(USUARIOS O GRUPOS)
    
    function AñadirChatID($data)
    {
        $file = fopen("Users/ChatIDs.txt", "a+");
        fwrite($file, $data . PHP_EOL);
        fclose($file);
    }

#FUNCION PARA SUBIR EL RANGO A PREMIUM(USUARIOS)
    
    function PremiumChatID($data)
    {
        $user = explode("|", $data)[0];
        $time = explode("|", $data)[1];
        $time = $time*24*3600;
        $time = time() + $time;
        $file = fopen("Users/Premium.txt", "a+");
        fwrite($file, $user.'|'.$time.'|'.time() . PHP_EOL);
        fclose($file);
    }

#FUNCION PARA SUBIR EL RANGO A ADMINISTRADOR(USUARIOS)
    
    function SetAdmin($data)
    {
        $file = fopen("Users/Admins.txt", "a+");
        fwrite($file, $data . PHP_EOL);
        fclose($file);
    }

#FUNCION PARA BANEAR USUARIOS
    
    function Ban($data)
    {
        $file = fopen("Users/Banned.txt", "a+");
        fwrite($file, $data . PHP_EOL);
        fclose($file);
    }

#FUNCION PARA DESBANEAR USUARIOS
    
    function Unban($data)
    {
        $file = array_values(array_unique(file('Users/Banned.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
        $out = [];
        foreach ($file as $line) {
            if ($line == $data) {
                $delete[] = $line;
            } else {
                $out[] = $line;
            }
        }
        $fp = fopen('./Users/Banned.txt', "w+");
        foreach ($out as $line) {
            fwrite($fp, $line . PHP_EOL);
        }
        fclose($fp);
    }

#FUNCION PARA VERIFICAR EL TIEMPO DE GRUPO
    
    function MyGroup($chatId)
    {
        global $Rank, $chatId, $username, $userId, $Rank_Group;

        if ($chatId == $userId) {
            $message = "Este comando es solo admitido para Grupos.";
            EnviarMensaje($chatId, $message, $message_id);
            exit();
        } elseif ($Rank_Group == "USER") {
            $tiempo_inicio =  "Vuelve Premium a tu Grupo y desbloquea nuevos comandos!\n";
            $tiempo_final = "";
            $texto3 = "🧨Si ocurre algun error habla a @KingProOficial";
        } else {
            $file = array_values(array_unique(file('Users/Premium.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));

            foreach ($file as $value) {
                $user_id = explode("|", $value)[0];
                $tiempo_inicio = explode("|", $value)[2];
                $tiempo_final = explode("|", $value)[1];
                if ($user_id == $chatId) {
                    $tiempo_inicio =  "📅Fecha de inicio de tu plan: ".date("d-m-Y", $tiempo_inicio)."\n";
                    $tiempo_final = "🗓Fecha de expiracion: ".date("d-m-Y", $tiempo_final)."\n";
                    $texto3 = "🧨Si ocurre algun error habla a @KingProOficial";
                    break;
                }
            }
        }

        $message = "👮 Grupo ID: $chatId \n$tiempo_inicio$tiempo_final$texto3";
        EnviarMensaje($chatId, $message, $message_id);
        exit();
    }

#FUNCION PARA VERIFICAR EL TIEMPO
    
    function MyAccount($userId)
    {
        global $Rank, $chatId, $username;

        if ($Rank == 'OWNER' || $Rank == 'ADMIN') {
            $tiempo_inicio = "📅Fecha de inicio de tu plan: No aplica para ". $Rank."s\n";
            $tiempo_final = "🗓Fecha de expiracion: No aplica para ". $Rank."s\n";
            $texto3 = "🧨Si ocurre algun error habla a @KingProOficial";
        } elseif ($Rank == 'USER') {
            $tiempo_inicio = "Vuelvete Premium y desbloquea nuevos comandos!";
            $tiempo_final = "";
            $texto3 = "\n🧨Si ocurre algun error habla a @KingProOficial";
        } else {
            $file = array_values(array_unique(file('Users/Premium.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));

            foreach ($file as $value) {
                $user_id = explode("|", $value)[0];
                $tiempo_inicio = explode("|", $value)[2];
                $tiempo_final = explode("|", $value)[1];
                if ($user_id == $userId) {
                    $tiempo_inicio =  "📅Fecha de inicio de tu plan: ".date("d-m-Y", $tiempo_inicio)."\n";
                    $tiempo_final = "🗓Fecha de expiracion: ".date("d-m-Y", $tiempo_final)."\n";
                    $texto3 = "🧨Si ocurre algun error habla a @KingProOficial";
                    break;
                }
            }
        }

        $message = "👮User: @".$username."[".$userId."]{".$Rank."}\n$tiempo_inicio$tiempo_final$texto3";
        EnviarMensaje($chatId, $message, $message_id);
        exit();
    }

#FUNCION PARA BANEAR USUARIOS
    
    function Delete($data)
    {
        $file = array_values(array_unique(file('Users/ChatIDs.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
        $out = [];
        foreach ($file as $line) {
            if ($line == $data) {
                $delete[] = $line;
            } else {
                $out[] = $line;
            }
        }
        $fp = fopen('./Users/ChatIDs.txt', "w+");
        foreach ($out as $line) {
            fwrite($fp, $line . PHP_EOL);
        }
        fclose($fp);
    }

#FUNCION PARA VERIFICAR CHAT-IDs(USUARIOS O GRUPOS)
    
    function VerificarChatID($chatId)
    {
        global $chatId, $username, $userId, $message_id, $My_ID, $Admin;

        $file = array_values(array_unique(file('Users/Banned.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
        foreach ($file as $key => $value) {
            if ($value == $userId) {
                $message = "‼️HAS SIDO BANEADO POR EL ADMIN DE ESTE BOT PORQUE HICISTE ALGO MALO‼️\nPara solicitar acceso contacta con @KingProOficial";
                EnviarMensaje($chatId, $message, $message_id);
                exit();
            }
        }

        if ($chatId == $My_ID || $userId == $My_ID) {
            $Rank = 'OWNER';
            $GLOBALS['Rank'] = $Rank;
        } elseif ($Admin == true) {
            $Rank = 'ADMIN';
            $GLOBALS['Rank'] = $Rank;
        } elseif ($chatId == $userId) {
            $file = array_values(array_unique(file('Users/ChatIDs.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
            foreach ($file as $key => $value) {
                if ($value == $userId) {
                    $verificacion = 'Añadido';
                    break;
                }
            }
            if ($verificacion == 'Añadido') {
                $file = array_values(array_unique(file('Users/Premium.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
                foreach ($file as $key => $value) {
                    $value_id = explode("|", $value)[0];
                    $Time = explode("|", $value)[1];
                    if ($value_id == $userId) {
                        $Rank = 'PREMIUM';
                        $GLOBALS['Rank'] = $Rank;
                        VerificarPremium($Time);
                        break;
                    } else {
                        $Rank = 'USER';
                        $GLOBALS['Rank'] = $Rank;
                    }
                }
            } else {
                $message = "‼️Hola bienvenido al mejor bot cheker ‼️ [@$username] [$userId]\n❌Lo sentimos pero para pedir acceso contacta a @KingProOficial\n✅Que tengas buen día!!!!";
                EnviarMensaje($chatId, $message, $message_id);
                exit();
            }
        } elseif ($chatId != $userId) {
            $file = array_values(array_unique(file('Users/ChatIDs.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
            foreach ($file as $key => $value) {
                if ($value == $chatId) {
                    $verificacion = 'Añadido';
                    break;
                } else {
                    $verificacion = 'No Añadido';
                }
            }
            if ($verificacion == 'No Añadido') {
                $file = array_values(array_unique(file('Users/ChatIDs.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
                foreach ($file as $key => $value) {
                    if ($value == $userId) {
                        $verificacion = 'Añadido';
                        break;
                    }
                }
            }
            if ($verificacion == 'Añadido') {
                $file = array_values(array_unique(file('Users/Premium.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
                foreach ($file as $key => $value) {
                    $value_id = explode("|", $value)[0];
                    $Time = explode("|", $value)[1];
                    if ($value_id == $chatId) {
                        $Rank_Group = 'PREMIUM';
                        $GLOBALS['Rank_Group'] = $Rank_Group;
                        VerificarPremiumGrupo($Time);
                        break;
                    } else {
                        $Rank_Group = 'USER';
                        $GLOBALS['Rank_Group'] = $Rank_Group;
                    }
                }
                $file = array_values(array_unique(file('Users/Premium.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
                foreach ($file as $key => $value) {
                    $value_id = explode("|", $value)[0];
                    $Time = explode("|", $value)[1];
                    if ($value_id == $userId) {
                        $Rank = 'PREMIUM';
                        $GLOBALS['Rank'] = $Rank;
                        VerificarPremium($Time);
                        break;
                    } else {
                        $Rank = 'USER';
                        $GLOBALS['Rank'] = $Rank;
                    }
                }
            } else {
                $message = "‼️Hola bienvenido al mejor bot cheker ‼️ [@$username] [$userId]\n❌Lo sentimos pero para pedir acceso contacta a @KingProOficial\n✅Que tengas buen día!!!!";
                EnviarMensaje($chatId, $message, $message_id);
                exit();
            }
        }
    }

#FUNCION PARA VERIFICAR PREMIUM
    
    function Premium()
    {
        global $chatId, $username, $userId, $message_id, $My_ID, $Admin, $Rank, $Rank_Group;
        if ($userId == $My_ID || $chatId == $My_ID || $Admin == true || $Rank_Group == 'PREMIUM' || $Rank == 'PREMIUM') {
        } else {
            $message = "‼️Hola, necesitas ser un Usuario o Grupo Premium para usar este comando ‼️ [@$username] [$userId]\n❌Lo sentimos pero para pedir acceso contacta a @KingProOficial\n✅Que tengas buen día!!!!";
            EnviarMensaje($chatId, $message, $message_id);
            exit();
        }
    }

#FUNCION PARA SUSTRAER CARD CON CUALQUIER COMANDO
    
    function GetCard($message)
    {
        $clean = explode(" ", $message)[1];
        return $clean;
    }

#FUNCION PARA CONSULTAR API
    
    function ConsultaAPI($Archivo, $Card)
    {
        $server = $_SERVER['SERVER_NAME'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://'.$server.'/bot@KingProOficial/Apis/'.$Archivo.'?lista='.$Card);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, []);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        $Resultado = curl_exec($ch);
        $GLOBALS['Resultado'] = $Resultado;
    }

#FUNCION PARA CONSULTAR API
    
    function Respuesta($Gateway, $Resultado, $Rank)
    {
        global $chatId, $username, $userId, $message_id, $Archivo, $Rank;

        if ($Gateway == 'CC Generator' || $Archivo == 'CardGenerator.php') {
            $Resultado = str_replace("-", "\n", $Resultado);
            $Resultado = "✅ NAMSO GENERATOR:\n".$Resultado;
            EnviarMensaje($chatId, $Resultado, $message_id);
        } else {
            preg_match_all('/\[(.*?)\] => (.*?)\./', $Resultado, $output_array);

            $x = 0;

            do {
                $array_nuevo[''.$output_array[1][$x].''] = $output_array[2][$x];
                $x++;
            } while (!empty($output_array[0][$x]));

            $Card = $array_nuevo['Card'];
            $Status = $array_nuevo['Status'];
            $Bin = $array_nuevo['Bin'];
            $Scheme = $array_nuevo['Scheme'];
            $Tipo = $array_nuevo['Tipo'];
            $Brand = $array_nuevo['Brand'];
            $Pais = $array_nuevo['Pais'];
            $Banco = $array_nuevo['Banco'];
            $Bandera = $array_nuevo['Bandera'];
            $Currency = $array_nuevo['Currency'];

            if ($Gateway == 'BIN Lookup' || $Archivo == 'BinLookup.php') {
                $message = "⚜️Bin Válido\n💳Bin: $Bin\n🧨Info: $Scheme - $Tipo - $Brand\n🏦Bank: $Banco\n🌐Country: $Pais $Bandera\n💸Currency: $Currency\n💣Checked By: @$username { $Rank }\n🤴Made by: @KingProOficial";
                EnviarMensaje($chatId, $message, $message_id);
            } else {
                $message = "💳Card -›› $Card\n⚡️Status -›› $Status\n‼️Gateway: $Gateway\n➖➖➖➖➖➖➖➖➖➖\n〰️〰️Detalles del bin〰️〰️\n🧨Bin -›› $Bin - $Scheme  - $Tipo - $Brand\n🏦Bank -›› $Banco\n💣Country -›› $Pais $Bandera - 💲$Currency\n〰️〰️〰️‹‹Info››〰️〰️〰️\n☑️Proxy -›› ???\n👁‍🗨Checked By -›› @$username { $Rank } \n🤴Bot By: @KingProOficial";
                EnviarMensaje($chatId, $message, $message_id);
            }
        }
    }
