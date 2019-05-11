<?php
session_start();
ob_start();
header('Content-Type: text/html; charset=utf-8');
$db = new mysqli("localhost","root","","adamasmaca");
$db->set_charset("utf8");
if($db->connect_errno){
    echo $db->connect_error;
    die("Veri Tabanı Baglantı Hatası");
}
$image_dizi = ["img/0.png","img/1.png","img/2.png","img/3.png","img/4.png","img/5.png"];
?>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Adam Asmaca</title>
    <style>
        #cerceve{
             width: 70%;
             min-width: 900px;
             margin: 25px auto;
        }
        #adamimg{
            margin-left: 50px;
            width: 229px;
            height: 360px;
            display: inline-block;
            float: right;
        }
        #soruTablosu{
            float: left;
        }
        .soruHarf{
            display: inline-block;
            width: 100px;
            height: 75px;
            border-bottom: 1px solid black;
            margin-left: 2px;
        }
        input[type=button]{
            box-shadow: none;
            border:none;
            width: 100px;
            height: 75px;
            font-size: 40px;
            display: none;
        }
        #cevapTablosu{
            margin-top: 50px;
            border: 1px solid lightgray;
            max-width: 476px;
            border-radius: 4px;
        }
        .harfler{
            text-align: center;
            display: inline-block;
            width: 50px;
            background: aliceblue;
            border: 1px solid black;
            text-decoration: none;
            color: black;
            border-radius: 4px;
            margin:2px;
        }
        .harfler:hover{
            background: darkgrey;
        }
        #tekrar{
            width: 90px;
            padding: 20px 30px;
            background: lightgray;
            display:block;
            color: black;
            margin: 0 auto;
            position: relative;
            border: 1px solid black;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div id="cerceve">
    <div id="soruTablosu">
        <?php
        $harf_tablosu = array();
        $_SESSION["bulunanlar"] = array();
        if(!isset($_GET["harf"])){
            $_SESSION["bitti"] = true;
            $_SESSION["can"] = 0;
            $_SESSION["image"] = $image_dizi[0];
            $_SESSION["won"] = 0;
            $sorular = array();
            $soru_sorgusu = $db->query("SELECT * FROM sorular");
            while ($veriler = $soru_sorgusu->fetch_assoc()){
                array_push($sorular,$veriler["soru"]);
            }
            $randMax = count($sorular)-1;
            $random = rand(0,$randMax);
            $soru = $sorular[$random];
            $soru_dizi = preg_split('//u', $soru, -1, PREG_SPLIT_NO_EMPTY);
            $_SESSION["soru"] = $soru_dizi;
            $bicim = $_SESSION["soru"];
            for($i = 0 ; $i < count($bicim); $i++){
                $bicim[$i] = '<div class="soruHarf">
                                <input type="button" value=" " style="display:block;">
                              </div>';
                echo $bicim[$i];
            }
            $_SESSION["bicim"] = $bicim;
        }
        else{
        if($_SESSION["won"] == count($_SESSION["soru"])){
            header("Location:index.php");
        }
            if($_SESSION["can"] != 5){
                $harf=$_GET["harf"];
                if(in_array($harf,$_SESSION["harfTablosu"])){
                    if(in_array($harf,$_SESSION["soru"])){
                        $_SESSION["won"]++;
                        if($_SESSION["won"] == count($_SESSION["soru"])){
                            $_SESSION["bitti"] = false;
                        }
                        for($i=0 ; $i < count($_SESSION["soru"]); $i++){
                            if($_SESSION["soru"][$i] == $harf){
                                $_SESSION["bicim"][$i] = '<div class="soruHarf"">
                                    <input type="button" value="'.$_SESSION["soru"][$i].'" style="display:block;">
                                </div>';
                            }
                        }
                        for($i = 0 ; $i < count($_SESSION["bicim"]) ; $i++){
                            echo $_SESSION["bicim"][$i];
                        }
                    }
                    else{
                        for($i = 0 ; $i < count($_SESSION["bicim"]) ; $i++){
                            echo $_SESSION["bicim"][$i];
                        }
                        $_SESSION["can"] = $_SESSION["can"]+1;
                        $_SESSION["image"] = $image_dizi[$_SESSION["can"]];
                        if($_SESSION["can"] == 5){
                            $_SESSION["bitti"] = false;
                        }
                    }
                }
                else{
                    header("Location:index.php");
                }
            }
            else{
                header("Location:index.php");
            }
        }
        ?>
        <div id="cevapTablosu">
            <?php
            if($_SESSION["bitti"]){
                for($i = 65 ; $i <= 90 ; $i++){
                    $deger = chr($i);
                    array_push($harf_tablosu,$deger);
                    echo'
                    <a class="harfler" href="index.php?harf='.$deger.'">'.$deger.'</a>
                    ';
                }
                array_push($harf_tablosu,"Ö");
                array_push($harf_tablosu,"İ");
                array_push($harf_tablosu,"Ş");
                array_push($harf_tablosu,"Ç");
                array_push($harf_tablosu,"Ğ");
                array_push($harf_tablosu,"Ü");
                $_SESSION["harfTablosu"]=$harf_tablosu;
                echo '
                <a class="harfler" href="index.php?harf=Ö">Ö</a>
            <a class="harfler" href="index.php?harf=İ">İ</a>
            <a class="harfler" href="index.php?harf=Ş">Ş</a>
            <a class="harfler" href="index.php?harf=Ç">Ç</a>
            <a class="harfler" href="index.php?harf=Ğ">Ğ</a>
            <a class="harfler" href="index.php?harf=Ü">Ü</a>
                ';
            }
            else{
                echo'
                <a id="tekrar" href="index.php">Tekrar Oyna</a>
                ';
            }
            ?>

        </div>
    </div>
    <div id="adamimg">
        <img src="<?php
        if(!isset($_SESSION["image"])){
            $resim = "img/0.png"; echo $resim;
        }
        else if($_SESSION["image"] == ""){
            $resim = "img/0.png"; echo $resim;
        }
        else{
            $resim=$_SESSION["image"]; echo $resim;
        }?>">
    </div>
</div>
</body>
</html>
<?php
$db->close();
?>
