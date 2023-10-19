<?php
// Bu dosyayı /modules/addons/zam/ dizinini oluşturup aktarıp daha sonra whmcs üzerinden aktif ediniz.
use WHMCS\Database\Capsule;

$pdo = Capsule::connection()->getPdo();
$pdo->beginTransaction();

function zam_config()
{
    return array(
        'name' => 'Ürün zamlama modülü',
        'description' => 'Ürünleri toplu yüzdelik zamlama modülü', // Description displayed within the admin interface
        'author' => '<a href="https://www.nosayazilim.com.tr/" target="_blank">Nosa Yazılım</a>',
        'language' => 'turkish',
        'version' => 1.1,
        'premium' => true,

    );
}
if (isset($_POST['zam'])) {
    $zam = $_POST["zam"];
    $products = $_POST["products"];
    try {
        $statement = $pdo->prepare('UPDATE tblhosting SET amount = amount * 1.' . $zam . ' WHERE packageid = ' . $products);

        $statement->execute();
        if ($pdo->inTransaction()) {
            $pdo->commit();
        }
    } catch (\Exception $e) {
        echo "Uh oh! {$e->getMessage()}";
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }
}
function zam_output($vars)
{
    $tblproducts = Capsule::table('tblproducts')->get();
    $s = "<div class='col-md-5'><select name='products' class='form-control'>";
    foreach ($tblproducts as $value) {
        $s .= "<option value='" . $value->id . "'>" . $value->name . "</option>";
    }
    $s .= "</select></div>";
    echo '<div class="row">
<form method="post" action="addonmodules.php?module=zam">
    ' . $s . '
  <div class="col-md-5">
    <input type="text" class="form-control" name="zam" aria-describedby="emailHelp" placeholder="% dilimi giriniz.">
  </div>
  <div class="col-md-2">
  <button type="submit" class="btn btn-primary">Uygula</button>
  </div>
</form></div>';
}

function zam_clientarea($vars)
{
    return "zam_clientarea";
}
