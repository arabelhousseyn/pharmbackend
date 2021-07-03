<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\categorieController;
use App\Http\Controllers\clientController;
use App\Http\Controllers\productController;
use App\Http\Controllers\commandeController;
use App\Http\Controllers\usersController;
use App\Http\Controllers\fournisseurController;
use App\Http\Controllers\commandeachatController;
use App\Http\Controllers\stockController;
use App\Http\Controllers\maincrmController;
use App\Http\Controllers\achatcrmController;
use App\Http\Controllers\compController;
use App\Http\Controllers\notificationController;
use App\Http\Controllers\usertokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/////////: auth api token section
Route::post('loginToken/',[usertokenController::class,'index'])->name('loginToken');
//1|XP9kLs2cwy2te78Q2nDuNSgBGdoPs6ldZiDlVKXY

Route::group(['middleware' => 'auth:sanctum'], function(){
    ///////////// start client section //////////

/////client section
Route::post('register/',[clientController::class,'registerClient'])->name('registerClient');
Route::post('verifytoken/',[clientController::class,'verifyToken'])->name('verifyToken');
Route::post('login/',[clientController::class,'login'])->middleware('loginclient')->name('login');
Route::post('client/',[clientController::class,'getClient'])->name('getClient');
Route::post('recovermail/',[clientController::class,'recoveryMail'])->name('recoveryMail');
Route::post('recovery/',[clientController::class,'recovery'])->name('recovery');
Route::post('clientbyusername/',[clientController::class,'getclientByName'])->name('getclientByName');
Route::post('updatebyclient/',[clientController::class,'updateClientById'])->name('updateClientById');
Route::post('changepass/',[clientController::class,'changePassword'])->name('changePassword');
Route::post('restrict/',[clientController::class,'restrict'])->name('restrict');
Route::post('insertsearch/',[clientController::class,'insertKEY'])->name('insertKEY');
Route::post('getproductsbysearch/',[clientController::class,'getsearch'])->name('getsearch');
Route::post('setreclamation/',[clientController::class,'setReclamation'])->name('setReclamation');

// product section
Route::get('categories/',[categorieController::class,'getAll'])->name('getCategorie');
Route::get('catss/',[categorieController::class,'getCtas'])->name('getCtas');
Route::post('exists/',[categorieController::class,'exists'])->name('exists');
Route::get('getproductbycategorie/',[productController::class,'getproductBycategorie'])->name('getproductBycategorie');
Route::post('getproductbyname/',[productController::class,'getproductByName'])->name('getproductByName');
Route::get('alerts/',[productController::class,'alert'])->name('alert');
Route::post('getlots/',[productController::class,'getLots'])->name('getLots');
Route::post('getlots2/',[productController::class,'getLots2'])->name('getLots2');
Route::post('cartdetails/',[productController::class,'cartDetails'])->name('cartDetails');
Route::post('count/',[productController::class,'getAllProductscount'])->name('getAllProductscount');
Route::post('searchByCategorie/',[productController::class,'searchByCategorie'])->name('searchByCategorie');
Route::post('puto/',[productController::class,'putoTmp'])->name('putoTmp');
Route::post('updatecartdetails/',[productController::class,'updateCartDetail'])->name('updateCartDetail');
Route::post('deleteproductcart/',[productController::class,'deleteProductCart'])->name('deleteProductCart');
Route::post('finalOrder/',[commandeController::class,'order'])->name('order');
Route::post('addphoto/',[productController::class,'addphoto'])->name('addphoto');
Route::post('delphoto/',[productController::class,'delphoto'])->name('delphoto');
Route::post('updatelot/',[productController::class,'updateLot'])->name('updateLot');
Route::post('formdcis/',[productController::class,'getformdci'])->name('formdcis');
Route::post('addlotprd/',[productController::class,'addlotforProduct'])->name('addlotforProduct');
Route::post('dellotprd/',[productController::class,'deletelotforproduct'])->name('deletelotforproduct');
Route::post('updateproduct/',[productController::class,'updateproduct'])->name('updateproduct');
Route::post('deleteproducts/',[productController::class,'deleteproducts'])->name('deleteproducts');
///// commande section
Route::post('commandebyclient/',[commandeController::class,'getcommandeByClinet'])->name('getcommandeByClinet');
Route::post('getlotsbycommande/',[commandeController::class,'getlotsByCommande'])->name('getlotsByCommande');
Route::post('deletecommandebyclient/',[commandeController::class,'deleteCommandeByClient'])->name('deleteCommandeByClient');
Route::post('getstatucommande/',[commandeController::class,'getstatu'])->name('getstatu');
Route::put('updateQuantityOfCommandeLots/',[commandeController::class,'updateQuantityOfCommandeLots'])->name('updateQuantityOfCommandeLots');
Route::post('deletelotforcommande/',[commandeController::class,'deletelotforcommande'])->name('deletelotforcommande');
Route::post('commandereceive',[commandeController::class,'receive'])->name('receive');

///////////// end client section //////////

/////////////// start admin panel section /////////

///// login 

Route::post('loginCheck/',[usersController::class,'checkUser'])->name('checkUser')->middleware('loginclient');
//// end login

//// users section

Route::post('users/',[usersController::class,'users'])->name('users');
Route::post('addadmin/',[usersController::class,'addAdmin'])->name('addAdmin')->middleware('admin');
Route::post('removeadmin/',[usersController::class,'removeAdmins'])->name('removeAdmins');
Route::post('updateadmin/',[usersController::class,'updateUser'])->name('updateUser')->middleware('admin');
Route::post('removead/',[usersController::class,'removeadmin'])->name('removeadmin');
Route::post('reactive',[usersController::class,'reactive'])->name('reactive');

/////////// fournisseur section
Route::get('fournisseurs/',[fournisseurController::class,'getAll'])->name('getAll');
Route::get('activefournisseurs/',[fournisseurController::class,'getactivefournisseur'])->name('getactivefournisseur');
Route::post('addfourni/',[fournisseurController::class,'addFourniseur'])->name('addFourniseur');
Route::post('updatefourni/',[fournisseurController::class,'updateFournisseur'])->name('updateFournisseur');
Route::post('removefournis/',[fournisseurController::class,'removebyselect'])->name('removebyselect');
Route::post('rfourni/',[fournisseurController::class,'removefournisseur'])->name('removefournisseur');
Route::post('reactivefourni/',[fournisseurController::class,'reactivefourni'])->name('reactivefourni');
/////////// commande achat
Route::get('getallcmds/',[commandeachatController::class,'getAll'])->name('cmsachatgetAll');
Route::get('getallproducts/',[productController::class,'getAll'])->name('productsgetAll');
Route::get('getproductsAll/',[productController::class,'allProductsjoin'])->name('allProductsjoin');
Route::get('detailProduct/{id?}',[productController::class,'detailsByProduct'])->name('detailsByProduct');
Route::post('updatePls/',[productController::class,'updatePls'])->name('updatePls');
Route::get('dcis/',[productController::class,'getAlldcis'])->name('getAlldcis');
Route::get('forms/',[productController::class,'getAllforms'])->name('getAllforms');
Route::post('addproduct/',[productController::class,'addProduct'])->name('addproduct');
Route::get('alllotss/',[productController::class,'getAlllots'])->name('getAlllots');
Route::post('addlots/',[productController::class,'addLot'])->name('addLot');
Route::post('save/',[commandeachatController::class,'save'])->name('save');
Route::post('removecartachat',[commandeachatController::class,'removecartachat'])->name('removecartachat');
Route::post('valid/',[commandeachatController::class,'order'])->name('orderachat');
Route::post('deletegrp/',[commandeachatController::class,'deleteByGroupe'])->name('deleteByGroupe');
Route::post('deletesingle/',[commandeachatController::class,'deleteSingle'])->name('deleteSingle');
Route::post('yesachat/',[commandeachatController::class,'yes'])->name('yesachat');
Route::post('noachat/',[commandeachatController::class,'no'])->name('noachat');
Route::post('detailcommandeachat/',[commandeachatController::class,'detailsCommande'])->name('detailsCommandeachat');
Route::post('detailupcommandeachat/',[commandeachatController::class,'detailforupdate'])->name('detailforupdate');
Route::post('detailbfacturecommandeachat/',[commandeachatController::class,'detailFactureCommande'])->name('detailfactureCommandeachat');
Route::post('paymentachat/',[commandeachatController::class,'payment'])->name('paymentachat');
Route::post('getpaymentachat/',[commandeachatController::class,'getpayment'])->name('getpaymentachat');
Route::get('getproductsjoincategorie/',[productController::class,'getproductsjoincategorie'])->name('getproductsjoincategorie');
Route::post('getdataforedit',[commandeachatController::class,'getdataforedit'])->name('getdataforedit');
Route::get('TotalcommandesAchatPaidandnotpaid',[commandeachatController::class,'TotalcommandesAchatPaidandnotpaid'])->name('TotalcommandesAchatPaidandnotpaid');
Route::post('searchcommande',[commandeachatController::class,'searchcommande'])->name('searchcommande');
///////////////// clients
Route::get('allClients/',[clientController::class,'getAllclient'])->name('getAllclient');
Route::post('addclient/',[clientController::class,'addClient'])->name('addClient');
Route::post('updateclient/',[clientController::class,'updateClient'])->name('updateClient');
Route::post('sussingleclient/',[clientController::class,'susingleClinet'])->name('susingleClinet');
Route::post('reactiveclient/',[clientController::class,'reactive'])->name('reactiveclient');
Route::get('reclamations/',[clientController::class,'getAllreclamation'])->name('getAllreclamation');
Route::post('feedback',[clientController::class,'feedback'])->name('feedback');
Route::post('feddbackbycommande',[clientController::class,'feddbackbycommande'])->name('feddbackbycommande');
Route::post('reclamationsbyclient/',[clientController::class,'getreclamantionsByClient'])->name('getreclamantionsByClient');
//////////////// commandes client
Route::get('commandesclient/',[commandeController::class,'getAllCommandes'])->name('getAllCommandes');
Route::post('lotsadmincommandesclient',[commandeController::class,'getlotsadminByCommandes'])->name('getlotsadminByCommandes');
Route::post('updateLotsCommande',[commandeController::class,'updateLotsCommande'])->name('updateLotsCommande');
Route::post('dellotcmd',[commandeController::class,'dellotcommande'])->name('dellotcmd');
Route::post('delcmdvente/',[commandeController::class,'delcmdvente'])->name('delcmdvente');
Route::post('nocmdvente/',[commandeController::class,'no'])->name('nocmdvente');
Route::post('yescmdvente/',[commandeController::class,'yes'])->name('yescmdvente');
Route::post('getliv/',[commandeController::class,'getliv'])->name('getliv');
Route::post('getfact/',[commandeController::class,'getFact'])->name('getfact');
Route::post('paymentvente/',[commandeController::class,'payment'])->name('paymentvente');
Route::post('getpaymentvente/',[commandeController::class,'getpayment'])->name('getpaymentvente');
Route::post('getcmdbyclientall',[commandeController::class,'getcommndesByClientall'])->name('getcommndesByClientall');
Route::post('cartByCommande',[commandeController::class,'cartByCommande'])->name('cartByCommande');
Route::get('totalvente',[commandeController::class,'Totalvente'])->name('Totalvente');
Route::get('TotalcommandesPaidandnotpaid',[commandeController::class,'TotalcommandesPaidandnotpaid'])->name('TotalcommandesPaidandnotpaid');
Route::post('searchcommandevente',[commandeController::class,'searchcommandevente'])->name('searchcommandevente');
Route::get('getSingleproductt',[productController::class,'getSingleproduct'])->name('getSingleproduct');
Route::post('addcommandeclient',[commandeController::class,'addcommandeclient'])->name('addcommandeclient');
///////////////// stock
Route::get('getstock/',[stockController::class,'getAll'])->name('getAllstock');
Route::post('addstock/',[stockController::class,'addstock'])->name('addstock');
Route::post('updatestock/',[stockController::class,'updateStock'])->name('updateStock');
Route::post('lotsproducts/',[productController::class,'getLotProductByStock'])->name('getLotProductByStock');
Route::post('eye/',[stockController::class,'eye'])->name('eye');
Route::post('slash/',[stockController::class,'slash'])->name('slash');
Route::post('upqt/',[stockController::class,'upqt'])->name('upqt');
Route::post('lotsprd/',[productController::class,'lotByProduct'])->name('lotByProduct');
Route::post('lotsprd2/',[productController::class,'lotByProduct2'])->name('lotByProduct2');
Route::post('addprdlotforstock/',[productController::class,'addproductlotForStock'])->name('addproductlotForStock');
Route::post('deleteprodbStock/',[productController::class,'deleteproductByStock'])->name('deleteproductByStock');
Route::post('stocksnegcurrent/',[stockController::class,'getStocksnegCurrent'])->name('getStocksnegCurrent');
Route::post('forward/',[stockController::class,'forward'])->name('forward');
//////////////// MAIN CRM
Route::get('ventejour',[commandeController::class,'venteJour'])->name('venteJour');
Route::get('ventemois',[commandeController::class,'ventemois'])->name('ventemois');
Route::get('achatjour',[commandeachatController::class,'achatjour'])->name('achatjour');
Route::get('achatmois',[commandeachatController::class,'achatmois'])->name('achatmois');
Route::get('countvente',[commandeController::class,'countAll'])->name('countvente');
Route::get('countachat',[commandeachatController::class,'countAll'])->name('countachat');
Route::get('countclient',[clientController::class,'countAll'])->name('countAllclient');
Route::get('countrec',[clientController::class,'countAllrec'])->name('countAllrec');
Route::get('mainvcrm',[maincrmController::class,'vente'])->name('mainventecrm');
Route::get('revenu',[maincrmController::class,'revenu'])->name('revenu');
Route::get('dacaht',[maincrmController::class,'dacaht'])->name('dacaht');
Route::get('nbrproduct',[maincrmController::class,'nbrproduct'])->name('nbrproduct');
Route::get('maincrmacaht',[maincrmController::class,'maincrmacaht'])->name('maincrmacaht');
Route::get('fns',[achatcrmController::class,'fournisseurs'])->name('fnns');
Route::get('stksrt',[compController::class,'stocksoritetotal'])->name('stocksoritetotal');
Route::get('stockss',[compController::class,'stocks'])->name('stocks');
Route::get('stken',[compController::class,'entrertotal'])->name('entrertotal');
Route::get('chartsortie',[compController::class,'chartsortie'])->name('chartsortie');
Route::get('chartenter',[compController::class,'chartenter'])->name('chartenter');
////////////: notification commandes
Route::get('getnotifications',[notificationController::class,'getNotifications'])->name('getNotifications');
Route::get('mark',[notificationController::class,'mark'])->name('mark');
///////////// end admin panel section //////////
    });






//// test & debug




