＊＊[DB設計]＊＊
localhost 
cafe_db
cafe_user
cafe_pass

/Applications/XAMPP/xamppfiles/bin/mysql -u root
mysql -u root

CREATE DATABASE cafe_db DEFAULT CHARACTER SET utf8;

GRANT ALL PRIVILEGES ON cafe_db.* TO cafe_user@'localhost' IDENTIFIED BY'cafe_pass' WITH GRANT OPTION;

USE cafe_db;

//商品テーブル
CREATE TABLE item (
  item_id int unsigned not null auto_increment,
  item_name varchar(100) not null,
  detail text not null,
  price int(10) unsigned not null,
  image varchar(50) not null,
  ctg_id tinyint unsigned not null,
  stock int unsigned default 0,
  primary key( item_id ),
  index item_idx( ctg_id )
);

//カート
CREATE TABLE cart (
  crt_id int unsigned not null auto_increment,
  customer_no int unsigned not null,
  item_id int unsigned not null,
  num tinyint(1) unsigned not null default 1,
  delete_flg tinyint(1) unsigned not null default 0,
  primary key( crt_id ),
  index crt_idx( customer_no, delete_flg )
);

//カテゴリー
CREATE TABLE category (
  ctg_id tinyint unsigned not null auto_increment,
  category_name varchar(100) not null,
  primary key(ctg_id)
);

//セッション
CREATE TABLE session (
  customer_no int unsigned not null auto_increment,
  session_key varchar(32),
  primary key(customer_no)
);

//会員情報
CREATE TABLE member (
  mem_id int unsigned not null auto_increment,
  family_name varchar(20) not null,
  first_name varchar(20) not null,
  family_name_kana varchar(20) not null,
  first_name_kana varchar(20) not null,
  user_name varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  sex tinyint(1) unsigned not null,
  year varchar(4) not null,
  month varchar(2) not null,
  day varchar(2) not null,
  zip1 varchar(3) not null,
  zip2 varchar(4) not null,
  address varchar(100) not null,
  email varchar(255) not null,
  tel1 varchar(6) not null,
  tel2 varchar(6) not null,
  tel3 varchar(6) not null,
  cue varchar(20) not null,
  contents text not null,
  regist_date datetime not null,
  update_date datetime,
  delete_date datetime,
  delete_flg tinyint(1) unsigned not null default 0,
  primary key(mem_id)
);

//郵便番号、住所等
CREATE TABLE postcode (
  jis varchar(5),
  old_zip varchar(5),
  zip varchar(7),
  pref_kana varchar(100),
  city_kana varchar(100),
  town_kana varchar(100),
  pref varchar(100),
  city varchar(100),
  town varchar(100),
  comment1 tinyint(1) unsigned,
  comment2 tinyint(1) unsigned,
  comment3 tinyint(1) unsigned,
  comment4 tinyint(1) unsigned,
  comment5 tinyint(1) unsigned,
  comment6 tinyint(1) unsigned
);


//商品口コミレビュー
CREATE TABLE reviews (
  review_id int unsigned NOT NULL auto_increment,
  review_comment text NOT NULL,
  review_date datetime NOT NULL,
  review_item_id int(10) NOT NULL,
  review_user_id int(10) NOT NULL,
  primary key(review_id)
);

//パスワード再設定
CREATE TABLE passreset (
  reset_id int unsigned NOT NULL auto_increment,
  token varchar(255) NOT NULL,
  date datetime NOT NULL,
  mem_id int(10) NOT NULL,
  primary key(reset_id)
);

//注文確定テーブル
CREATE TABLE orderconfirmed (
  order_id int unsigned NOT NULL auto_increment,
  mem_id int(10) NOT NULL,
  date datetime NOT NULL,
  item_id int(10) NOT NULL,
  sumnum int(10) unsigned not null,
  sumprice int(10) unsigned not null,
  delete_flg tinyint(1) unsigned not null default 0,
  primary key(order_id)
);

//カテゴリー
INSERT INTO category (
  category_name 
) VALUES
('Coffee Beans'),
('Original Goods'),
('Coffee Tools'),
('Coffee Gift'),
('Coffee Cup');

//ショッピングサイト内商品（アイテム数52）
INSERT INTO item (
  item_id ,
  item_name ,
  detail ,
  price ,
  image ,
  ctg_id ,
  stock
) VALUES
( 1, 'グワテマラ アンティグア', 'チョコレートや微かなスパイス、上品な酸味が重なる、複雑ながらもエレガントで洗練されたコーヒー豆です', 1280 , 'beans1.jpg', 1 ,10),
( 2, 'フレンチロースト', '高温でのローストに耐えられる質の高い豆を選びすぐり、ぎりぎりまでローストした最も深煎りの、強烈なスモーキーさが特徴のブレンドコーヒー豆です', 1480, 'beans2.jpg', 1 ,10),
( 3, 'ハウスブレンド/ディオブレンド', '当店自慢のオリジナルブレンドで、エスプレッソよりも少し深みのある甘みとほのかに薫る大地のような力強さが特徴のコーヒー豆です。',1780 , 'beans3.jpg', 1 ,10),
( 4, 'caotina powder 500g缶', 'スイス産のチョコレートパウダーです。当店で扱うコーヒーによくマッチし、ほんのり苦味があるのが特徴です。牛乳やケーキなど使い道も様々。直接取り引き寄せて扱っているため在庫数に限りがございます。', 650, 'original1.jpg', 2 ,10),
( 5, 'コーヒーフレッシュ', 'コーヒーの深みを加工した調味料です。オニオンチップのような食感で、パスタの調理に組み合わせ流ことでコクに深みがでます。', 2480 , 'original3.jpg', 2 ,10),
( 6, '珈琲消臭セット', '珈琲抽出後の豆を再利用した消臭用の豆です。加工後、匂いが長持ちします。トイレや玄関などお洒落に消臭用としてお使いください。', 500 , 'original2.jpg', 2 ,10),
( 7, 'コーヒーカップ', '深みのあるカラーのマグカップ。内外の塗分けと日常的に使いやすいシンプルなデザインが特徴です。コーヒーやカフェオレ、紅茶などティータイムに是非', 770 , 'cup5.jpg', 5 ,10),
( 8, 'coffee Tool', '珈琲好きの多いノルウェーの街で生産されており、豆を自宅でもノルウェーの煎れ方で本格的に味わうことができます。好みのブレンドでお楽しみください。', 5480 , 'tools2.jpg', 3 ,10),
( 9, 'コロンビア', 'アンデス山脈の高い標高と安定した気候で育てられた地域限定のコーヒです。浅煎りローストで爽やかな酸味が特徴です。濃い味わいをお楽しみください。', 1180 , 'beans5.jpg', 1 ,10),
( 10, 'パナマ エスメラルダ ゲイシャ', '柑橘系の爽やかな香りと口にしてもやさしく漂う甘酸っぱい風味が独特です。雑味のない透明感の有る味わいを楽しめます。挽かなくても存在感の有る香りが漂います。', 1900 , 'beans6.jpg', 1 ,10),
( 11, 'コーヒーメーカー全自動ミル付き', '豆挽きからドリップまでこれ1台におまかせ。省スペースなのでスマートに収まる。ステンレスメッシュフィルター採用。最大220mlまで抽出可能。レトロで淡いかわいいデザイン。', 8800 , 'tools1.jpg', 3 ,10),
( 12, '保温ステンレスサーバー', 'ワンタッチで分離できてお手入れ簡単!!丈夫で割れない ステンレスまほうびんホットからアイスまでオールシーズン使える「高い保温・保冷力」があります。', 3300 , 'tools3.jpg', 3 ,10),
( 13, 'マンデリン', 'インドネシア スマトラ島産マンデリンならではの独特な香り・やわらかな苦味の中に甘味を感じるコーヒーです。', 1160 , 'beans7.jpg', 1 ,10),
( 14, 'ブルーマウンテン', 'コーヒーの王様と言われるほど、酸味、苦味、甘みのバランスのとれた味わいです。自家焙煎コーヒー豆、当店自慢の一豆となっております。', 2000 , 'beans8.jpg', 1 ,10),
( 15, 'アイスグラス【店内仕様】', '店内で使用しているアイスコーヒーのグラスになります。お客様から取り扱って欲しいという要望に答え、同じデザインの容器です。', 770 , 'cup6.jpg', 5 ,10),
( 16, 'タンブラー式カップ', '熱を伝えにくいステンレス真空二重構造。飲みごろを保って、結露しにくいスグレものです。', 1600 , 'cup2.jpg', 5 ,10),
( 17, '包装 HappyBirthday', '誕生日ギフト用の包装をメモ用紙同封でさせていただきます。（単品購入も可能です）', 200 , 'gift1.jpg', 4 ,10),
( 18, 'シングルマグカップ', 'こちらのカップは1.5杯分入るカップで、自宅でアメリカンや多く飲まれる方など使い勝手良いです。', 880 , 'cup4.jpg', 5 ,10),
( 19, '有機珈琲フェアトレードモカブレンド', '青い果実のようなフレッシュな香りと独特の酸味が特徴です。飲みやすいのであまりコーヒーを飲まない人でもおいしく飲むことができます。', 850 , 'beans14.jpg', 1 ,10),
( 20, 'キリマンジャロ', '甘さを感じる良質の酸味が特徴で、コクが強くケーキとともに、また食後にピッタリのコーヒーです。, キリマンジャロらしさを味わうことができます。', 1050 , 'beans11.jpg', 1 ,10),
( 21, 'コーヒーカップ', 'カフェにふらっと立ち寄ったような気分が楽しめる、おしゃれなカップ。インディアン要素入ってます！', 770 , 'cup7.jpg', 5 ,10),
( 22, 'ドリップ式コーヒーメーカー', 'ご家庭で簡単に本格コーヒーが楽しめるドリップ式コーヒーメーカー最大5杯分を自動でドリップできます。挽いたコーヒー豆をセットするだけなので忙しい朝でもストレスフリー。ドリップ終了後も自動で保温が続くのでいつでも温かいコーヒーを楽しめます。', 4500 , 'tools4.jpg', 3 ,10),
( 23, 'ペアマグカップ', 'お二人にぴったりなペアのマグカップです。セラミック製で電子レンジ・食器洗い機もご使用いただけます。', 1300 , 'cup3.jpg', 5 ,10),
( 24, 'ジャワアラビカ コーヒー豆', '苦味が強いものの酸味はほとんどないため、コーヒーは苦いのが好きという人におすすめです。アイスコーヒーとして飲むのにも適しています。', 970 , 'beans15.jpg', 1 ,10),
( 25, 'アラミド ジャコウネコ', 'ジャコウネコのコーヒーの特徴である複雑で豊かな風味と香りを、最大限に味わって頂くために、生豆の状態で輸入しております。日本で自家焙煎を行うことで、ロースト仕立ての豆を提供しております。', 7800 , 'beans12.jpg', 1 ,10),
( 26, 'コスタリカ コーラル・マウンテン', '焙煎度は中煎りで、カリブ海の珊瑚を思わせる豊かな大自然によって生み出された美しい酸味が特徴です。', 780 , 'beans13.jpg', 1 ,10),
( 27, 'エチオピア イルガチェフェ', 'エチオピアのコーヒーは、紅茶にも似た「モカフレーバー」が特徴ですが、特にこのイルガチェフェは、水洗式でしか得られない華やかで透明感のあるクリーンな風味です。まるでレモンティーのような爽やかな香りに加え、花のような芳香なアロマも感じられる香味重視のコーヒーです。', 780 , 'beans10.jpg', 1 ,10),
( 28, 'チョコレート', 'ギフト用でもありますが、そのままお楽しみいただけます。深みあり甘すぎない味わいが、当店で扱っている豆にとてもよく合います。是非一緒にお楽しみ頂ければと思います', 830 , 'gift3.jpg', 4 ,10),
( 29, 'ギフトカード 母の日', '一見母の日限定と思いきやそんなことはありません。贈り物と一緒にデザインを足してみるのもありでしょう。', 50 , 'gift2.jpg', 4 ,10),
( 30, '手挽きコーヒーミル', '機械式のコーヒーミルと比べると、手間もかかる上に力も必要ですが、低速で豆を挽くため摩擦熱がでにくく、コーヒー豆が酸化しにくくなります。引き立ての珈琲を自宅でお楽しみ頂けます', 3300 , 'tools6.jpg', 3 ,10),
( 31, 'ハワイコナコーヒー 200g', 'ハワイ島の西海岸に位置するコナ地域で生産されている希少なコーヒー豆です。生産地の山麓には、コーヒーベルトと呼ばれる土地があり、ここで育てられたもの以外はコナコーヒとは呼べません。コナコーヒー特有の豊かな香り、確かな味わいが極上の時間を演出してくれます。', 5600 , 'beans4.jpg', 1 ,10),
( 32, 'シングルマグカップ', '白と黒のシンプルなデザインのマグカップです。コーヒーカップは分厚めなので熱くなりにくく、持ち手の大きさもちょうど良いです。', 2200 , 'cup1.jpg', 5 ,10),
( 33, 'ブラジル 手摘み完熟豆', '一粒一粒熟練の生産者により選び出された完熟のチェリーは、甘味に優れ高い品質と人気を誇ります。甘味のあるチョコレートフレーバー、冷めるに従い増す芳醇なコク、そして雑味の無いクリアな後味が特徴です。', 1480 , 'beans16.jpg', 1 ,10),
( 34, '木材テーブル', 'インスタ映えにも使える。木材テーブルのオリジナルノベルティ。インテリアとしてぜひ', 980 , 'original4.jpg', 2 ,5),
( 35, 'タンザニア キゴマディープブルーAA', 'コーヒーの栽培に適した「肥沃で柔らかい赤土」が素晴らしいコーヒーを作り出し、芳醇な酸味とフルーティな香りがとても魅力的なコーヒーに仕上がっています。', 1480 , 'beans17.jpg', 1 ,10),
( 36, 'ビダーブレンド', 'マンデリンをベースにした深煎りほろ苦のブレンドです。苦味の中にほのかな甘味と深い薫りを感じていただけます。 濃厚なコクとダークチョコレートのような余韻が口の中に残ります。ミルクとの相性がいいので、カフェオレのベースとしてもお薦めです。', 980 , 'beans18.jpg', 1 ,10),
( 37, 'セラーメイト', 'コーヒーミルク入れにぴったり。中身が見えるので家庭で調味料入れにもお使いいただけます。', 440 , 'original6.jpg', 2 ,5),
( 38, 'ピュアブレンド', '毎日飲んでも飽きのこない、上品ですっきりとした口当たりとやや軽めにローストしたタンザニア（キリマンジャロ）とコロンビアをバランス良く、ブレンドしました。上質なコーヒーが持つコーヒー本来のフルーティな薫りがお楽しみいただけます。', 980 , 'beans19.jpg', 1 ,10),
( 39, 'モカイルガチェフ', '標高2,000ｍ前後のイルガチェフ地区で栽培されたこのコーヒーは、エチオピアコーヒーの中でも群を抜く上品質であり、まさにコーヒーの源流というにふさわしいモカコーヒーです。甘く爽やかなモカフレーバーにまろやかな酸味が印象的です。', 1380 , 'beans20.jpg', 1 ,10),
( 40, 'ミニコーヒープレス', 'お手軽でかつちょうどいいサイズ感です。お一人で好きな時にご自身で抽出を行えます。コーヒーフィルター要らずで目盛りが付いている為、そのまま味わってお楽しみ頂けます。', 980 , 'tools7.jpg', 3 ,5),
( 41, 'てんとう虫柄カップ', '夏を象徴させるてんとう虫柄のグラス。喫茶店となんともミスマッチですが、一周回ってありなんてことも。。夏限定で店内で使用しております。', 660 , 'cup8.jpg', 5 ,10),
( 42, 'チョコケーキ風ブラウニー', 'どのコーヒにも合う。そんなチョコケーキ風ブラウニーです。店内でもコーヒーとご一緒に楽しまれるお客様も多くいます。店内で切り分けてお出ししておりますが、お家で楽しみたいという要望に答え、切り分け前のサイズを1セットとして通販にも出すことにしました。甘すぎずそっとコーヒーの苦味を引き立たせてくれます。', 1800 , 'gift4.jpg', 4 ,3),
( 43, 'パーカスコーヒー豆', 'エル サルバドル生まれの突然変異種のパーカス。風味はレモンの爽やかな風味。繊細な酸と滑らかなボディ。ハチミツのような余韻と甘みが残るのが特徴です。', 1550 , 'beans21.jpg', 1 ,10),
( 44, 'ブラジルショコラ（中挽き）', 'ナッツやチョコレートのような香りが特徴で、豊かなフレーバー、上質の甘みが広がります。クリーンでしっかりとした口当たりと心地よい渋みや甘みが広がるコーヒーです。', 880 , 'beans22.jpg', 1 ,10),
( 45, 'MINERAGUA 炭酸水', 'メキシコでは市販の炭酸水です。店頭販売もしております。', 450 , 'original7.jpg', 2 ,5),
( 46, '店内グラス', '店内で使用しているオリジナルのグラスです。店内では挽きたての豆と牧場から引き寄せた100％牛乳を混ぜ、コーヒー牛乳も出しております。お家でも同じグラスでお楽しみ頂ければ店主も大喜び！', 750 , 'cup9.jpg', 5 ,10),
( 47, 'ムンドノーボコーヒー豆250g', '100年の歴史を持つ名門カフェ・ランジャ農園で手摘み収獲されたアラビカ種ムンドノーボ（新世界の意）品種の完熟豆は豊かなフレーバーと苦味・酸味のバランスが良く雑味のないクリアーな味の逸品です。', 2200 , 'beans23.jpg', 1 ,10),
( 48, 'コーヒー牛乳（店内オリジナル豆使用）', '当店人気商品です。1杯分を容器に入れそのままお届け。豆はもちろん牛乳も提携している牧場の新鮮かつ100％の生乳です。', 380 , 'original8.jpg', 2 ,40),
( 49, '真空セラーメイト', '挽いた豆、もしくはそのままの豆を移し替えても真空にして持ち運べる優れ物。一緒についてくる小道具も海外を感じさせます。', 2980 , 'tools8.jpg', 3 ,5),
( 50, 'アラビカン250g', '上質なアラビカコーヒーのみを100%使用し、ミディアム（中煎り）に近いミディアムストロングロースト（中深煎り）でじっくりゆっくりスローロースト。 香ばしくスィートな酸味が爽やかで飲みやすく人気のブレンドコーヒーです。', 950 , 'beans24.jpg', 1 ,10),
( 51, 'ステンレスポットセット', 'モダンでお洒落なデザインで、軽くアウトドアする場合でも持ち運び楽です。お家だけでなくお外でも楽しみたい方にはピッタリのセットです。', 1980 , 'cup10.jpg', 5 ,10),
( 52, 'マラゴジッペ種豆500g', '生産量が少ない品種のコーヒーです。特徴としてはフルーティーな香り、マイルドな甘みとすっきりとした酸味のバランスの良さ、さらに爽やかさな後味があります。朝に好まれて飲まれる方も！', 3500 , 'beans25.jpg', 1 ,10);
<--------------------------------------------------------------------->
INSERT INTO item VALUES ( 商品ID, '商品名', '商品説明', 金額 , '画像', カテゴリーID, 在庫数);
INSERT INTO item VALUES ( ,  '', '',  , '.jpg',  ,  );
<-------------------------------------------------------------------->