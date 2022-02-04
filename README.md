# Taro Events

Tags: events, posts
Contributors: tarosky, ko31
Tested up to: 5.8  
Requires at least: 5.4  
Requires PHP: 5.6  
Stable Tag: nightly  
License: GPLv3 or later  
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

イベント情報に関する機能を提供するプラグインです。

## 概要

主な機能は以下のもの。

- カスタム投稿タイプ「イベント」、カスタムタクソノミー「イベントカテゴリー」「イベント種別」を追加
- イベントアーカイブの絞り込みフォームの設置
- イベント詳細ページに構造化データ（JSON-LD）を出力
- RSS フィードにイベント情報を追記

言語は英語、日本語（Poeditで翻訳）に対応済みです。

## 主な用途

- サイトでイベント情報を管理したい。

## 設定方法

- GitHub から最新の zip ファイルをダウンロードし、管理画面からインストール・有効化する。
    - https://github.com/tarosky/taro-events/releases/latest
- 「設定」→「パーマリンク」を開き、「変更を保存」を実行する。（リライトルールを更新しておく）
- メニューに「イベント」が追加されるので、そこからイベント情報を入稿する。


## 機能仕様

### メニュー

プラグインを有効化すると、カスタム投稿タイプ：イベント（event）のメニューが表示されます。

- イベント
    - カスタム投稿タイプ：イベント（event）
- イベントカテゴリー
    - カスタムタクソノミー：イベントカテゴリー（event-category）
    - 用途に決まりはありませんが、例えば「公開イベント」「会員向けイベント」など付けるイメージ。
- イベント種別
    - カスタムタクソノミー：イベント種別（event-type）
    - 用途に決まりはありませんが、例えば「セミナー」「交流会」など付けるイメージ。

### カスタムフィールド

イベントの編集画面にて、イベント情報に関するカスタムフィールドが入力できるようになります。

なお、デフォルトで用意しているカスタムフィールドは、汎用的に使えてかつ情報発信する上でメリットがあるという観点で、[Googleのイベント構造化データ](https://developers.google.com/search/docs/advanced/structured-data/event)のページを参考に決めています。

以下、表示されるカスタムフィールドです。カッコ内はカスタムフィールド名（meta_key）。

- 基本設定
    - 名称（`_event_name`）
    - 説明（`_event_description`）
    - イベントステータス（`_event_status`）
        - 構造化データに出力するための値で、後述の絞り込みフォームのイベントステータスとは別物なので注意。
- 日付
    - 開始日（`_event_start_date`）、時間（`_event_start_date_time`）
    - 終了日（`_event_end_date`）、時間（`_event_end_date_time`）
    - 受付開始日（`_event_reception_start_date`）、時間（`_event_reception_start_date_time`）
    - 受付終了日（`_event_reception_end_date`）、時間（`_event_reception_end_date_time`）
- 場所
    - オフラインイベントかどうか（`_event_is_offline`）
    - 会場名（`_event_location_name`）
        - オフラインイベントの会場名
    - 住所（`_event_location_address`）
        - オフラインイベントの会場住所
    - オンラインイベントかどうか（`_event_is_online`）
    - URL（`_event_location_url`）
        - オンラインイベントに参加できるURL
- 申し込み
    - 利用状況（`_event_offers_availability`）
    - 価格（`_event_offers_price`）
    - 通貨（`_event_offers_currency`）
    - チケット発売開始日時（`_event_offers_valid_from`）
    - URL（`_event_offers_url`）
- 主催者
    - 種別（`_event_organizer_type`）
    - 名称（`_event_organizer_name`）
    - URL（`_event_organizer_url`）
    
#### 入力方法についての補足

- 時間が決まっていない終日イベントを作りたい場合は、開始日や終了日の時間フィールドは未入力にしてください。そうすることで、絞り込みフォームでのイベントステータス判定時に開始日が「00:00:00」、終了日が「23:59:59」という時間で扱われるので、その日一杯が有効期間として処理されるようになります。
- いずれの項目も入力は任意ですので、サイトで必要な情報に合わせて入力してください。（ただし、後述の構造化データを出力するには、いくつかの項目が入力必須となります。）

#### カスタムフィールドの値を参照

テーマでカスタムフィールドの値を使用する際は、WP 標準の `get_post_meta` 関数で値を取得できます。

```
// get_post_meta 関数でイベント名称を取得
$event_naem = get_post_meta( get_the_ID(), '_event_name', true );
```

また、プラグインが用意している関数を使用することもできます。

```
// イベント名称を取得
$event_name = taro_events_get_meta( '_event_name', $post );

// イベント名称を取得（現在の投稿を取得する場合は第2引数を省略可）
$event_name = taro_events_get_meta( '_event_name' );

// 全てのイベント情報カスタムフィールドを取得
$event_name = taro_events_get_metas();
```

### 絞り込みフォーム

イベントアーカイブの出力結果を絞り込み表示するためのフォームを設置できます。

以下いずれかの方法で、フォームを設置することができます。

- 関数 `taro_events_get_filter_form`
    - テンプレートファイル内の任意の位置でこの関数を呼び出して、フォームを表示させる。
    - （例）テンプレート `archive-event.php` 内に記述して、アーカイブページにフォームを表示する。
- ショートコード `[taro-event-filter-form]`
    - 編集画面のエディタからショートコードを埋め込んで、フォームを表示させる。
    - （例）TOPページなど、任意の場所にフォームを表示する。

表示されるフォームは特にレイアウトされていませんので、使用するサイトの css で調整をしてください。

フォームの項目は以下のもの。

- イベントカテゴリー
    - タクソノミー「イベントカテゴリー」を条件とします。
- イベント種別
    - タクソノミー「イベント種別」を条件とします。
- イベントステータス
    - 以下の選択項目を条件とします。
        - 受付中（accepting）
        - 開催中（opening）
        - 開催終了（finished）
    - 受付中：イベント情報の受付開始日、受付終了日と照合して絞り込みが行われます。
    - 開催中、開催終了：イベント情報の開始日、終了日と照合して絞り込みが行われます。

フォームをどこに設置した場合でも、絞り込みを実行するとイベントアーカイブページに遷移して、絞り込み結果が一覧表示されます。


#### 絞り込みフォームのカスタマイズ

絞り込みフォームの表示内容は、以下の方法でカスタマイズすることが可能です。

- フォームの html をフィルターフック `taro_events_get_filter_form_html` で書き換える。
- テーマフォルダに `taro_event_filter_form.php` ファイルを設置して、フォームのテンプレートを上書きする。


### 構造化データ

イベントの詳細ページ表示する際、`<head>` タグ内にイベント用の構造化データ（JSON-LD）が出力されます。

以下、出力例です。

```
<script type="application/ld+json">
{"@context":"http:\/\/schema.org","@type":"Event","name":"test1","startDate":"2022-01-25T09:00:00+09:00","endDate":"2022-02-06","location":[{"@type":"Place","name":"The Excellent Place","address":{"@type":"PostalAddress","name":"Tokyo"}},{"@type":"VirtualLocation","url":"https:\/\/example.com"}]}
</script>
```

構造化データには、イベント情報に入力されたカスタムフィールドの値が出力されており、以下の場合は出力が行われません。

- 開始日、終了日の日付が入力されていない。
- オフラインイベントかどうか、オンラインイベントかどうか、がどちらも選択されていない。
- オフラインイベントの場合、イベント会場名、イベント住所が入力されていない。
- オンラインイベントの場合、オンラインイベントURLが入力されていない。

※上記は [Googleのイベント構造化データ](https://developers.google.com/search/docs/advanced/structured-data/event?hl=ja)のページで必須項目とされている項目のため、未入力時は出力しない処理としています。

#### 構造化データのカスタマイズ

`taro_events_is_display_json_ld` フィルターを使って、構造化データを出力しないように変更できます。

```
// 構造化データを出力しない
add_filter( 'taro_events_is_display_json_ld', function() {
    return false;
} );

```

### RSS配信

WordPress 標準の RSS2.0 フィードに、イベント情報のカスタムフィールドを追記した RSS 配信を行います。

https://example.com というサイトの場合、RSS は下記 URL となります。

- イベント全体
    - https://example.com/event/feed/
- イベントカテゴリー
    - https://example.com/event-category/[イベントカテゴリーのスラッグ]/feed/
- イベント種別
    - https://example.com/event-type/[イベント種別のスラッグ]/feed/

RSS に出力されるイベント情報の項目は以下のものです。

- 開始日（`<ev:startdate>`）
- 終了日（`<ev:enddate>`）
- 場所（`<ev:location>`）
- 主催者（`<ev:organizer>`）
- イベント種別（`<ev:type>`）
    - 複数ある場合はカンマ区切りで出力
- アイキャッチ画像（`<enclosure>`）


## フック・関数

プラグインが用意しているフックは `apply_filters`、`taro_events_` などでソースコードを検索すると探せるかと思います。

そのうち、比較的使う可能性がありそうなものをいくつか挙げてみます。

- `taro_events_post_type`
    - カスタム投稿タイプ：イベントのスラッグ名を変更したい場合（デフォルト： `event` ）
- `taro_events_post_type_args`
    - カスタム投稿タイプ：イベントの初期化パラメータを変更したい場合
- `taro_events_taxonomy_event_category`
    - カスタムタクソノミー：イベントカテゴリーのスラッグ名を変更したい場合（デフォルト： `event-category` ）
- `taro_events_taxonomy_event_type`
    - カスタムタクソノミー：イベント種別のスラッグ名を変更したい場合（デフォルト： `event-type` ）
- `taro_events_is_available_filter_event_category`
    - 絞り込みフォームにイベントカテゴリー選択を使用するかどうか変更したい場合（デフォルト：使用する）
- `taro_events_is_available_filter_event_type`
    - 絞り込みフォームにイベント種別選択を使用するかどうか変更したい場合（デフォルト：使用する）
- `taro_events_is_available_filter_event_status`
    - 絞り込みフォームにイベントステータス選択を使用するかどうか変更したい場合（デフォルト：使用する）


プラグインで用意している関数は `includes/functions.php` ファイルに入っているので、詳しくはそちらをご覧ください。

そのうち、比較的使う可能性がありそうなものをいくつか挙げてみます。

- `taro_events_is_event_accepting`
    - 現在のイベント情報が「受付中」かどうかを判定（`true`：受付中、`false`：受付中でない）
- `taro_events_is_event_opening`
    - 現在のイベント情報が「開催中」かどうかを判定（`true`：開催中、`false`：開催中でない）
- `taro_events_is_event_finished`
    - 現在のイベント情報が「終了」かどうかを判定（`true`：終了、`false`：終了してない）


## カスタマイズ方法

以下、要件別にカスタマイズ方法の事例を紹介します。


### イベントのカスタム投稿でタグを使えるようにする

```
// カスタム投稿イベントのパラメータにフックで追記
function create_custom_taxonomy( $args ) {
	// 標準のタグを使えるように指定
	$args['taxonomies'] = [ 'post_tag' ];

	return $args;
}

add_action( 'taro_events_post_type_args', 'create_custom_taxonomy' );
````

### イベント編集画面で不要なカスタムフィールドを非表示にする

例えば、基本 > 名称 のフィールドを非表示にする場合、下記のようなフックを記述します。


```
/*
 * 非表示にしたいカスタムフィールドを設定するフィルターフック
 */
add_filter( 'taro_events_unavailable_meta_keys', function ( $meta_keys ) {

	// 以下の配列 $meta_key に設定した項目が編集画面で非表示になります。

	$meta_keys[] = '_events_name'; // 基本：イベント名称

	return $meta_keys;
} );
```

仮に下記のようにすると、全部のフィールドが非表示になります。

```
add_filter( 'taro_events_unavailable_meta_keys', function ( $meta_keys ) {

	$meta_keys[] = '_events_name';                   // 基本：イベント名称
	$meta_keys[] = '_events_description';            // 基本：説明
	$meta_keys[] = '_events_event_status';           // 基本：イベントステータス
	$meta_keys[] = '_events_start_date';             // 日付：開始日（時間もセットで非表示）
	$meta_keys[] = '_events_end_date';               // 日付：終了日（時間もセットで非表示）
	$meta_keys[] = '_events_reception_start_date';   // 日付：受付開始日（時間もセットで非表示）
	$meta_keys[] = '_events_reception_end_date';     // 日付：受付開始日（時間もセットで非表示）
	$meta_keys[] = '_events_is_offline';             // 場所：オフラインイベント
	$meta_keys[] = '_events_location_name';          // 場所：会場名
	$meta_keys[] = '_events_location_address';       // 場所：会場住所
	$meta_keys[] = '_events_is_online';              // 場所：オンラインイベント
	$meta_keys[] = '_events_location_url';           // 場所：会場URL
	$meta_keys[] = '_events_offers_availability';    // 申し込み：利用状況
	$meta_keys[] = '_events_offers_price';           // 申し込み：価格
	$meta_keys[] = '_events_offers_currency';        // 申し込み：通貨
	$meta_keys[] = '_events_offers_valid_from';      // 申し込み：チケット発売開始日（時間もセットで非表示）
	$meta_keys[] = '_events_offers_url';             // 申し込み：URL
	$meta_keys[] = '_events_organizer_type';         // 主催者：種別
	$meta_keys[] = '_events_organizer_name';         // 主催者：名称
	$meta_keys[] = '_events_organizer_url';          // 主催者：URL

	return $meta_keys;
} );
```

### 任意のカスタムフィールドを追加する

イベント編集画面に任意のカスタムフィールドを追加する方法です。

例えば、日付グループの下部にテキストエリアを追加する場合、下記のような2つのフックを記述します。

```
/*
 * 日付グループの下部に独自のカスタムフィールドを表示するアクションフック
 *
 * ・フィールド名には接頭語に「_events_」を付けた名称を付けてください。（下記の例では「_events_extra」）
 */
add_action( 'taro_events_bottom_metabox_date_group', function ( $post_id ) {
	?>
	<tr>
		<th><label for="_events_extra">追加フィールド名</label></th>
		<td>
			<textarea rows="5" class="large-text code" name="_events_extra"
			          id="_events_extra"><?php echo esc_textarea( get_post_meta( $post_id, '_events_extra', true ) ); ?></textarea>
		</td>
		</td>
	</tr>
	<?php
} );

/*
 * 独自のカスタムフィールド名を追加するフィルターフック
 */
add_filter( 'taro_events_get_meta_keys', function ( $meta_keys ) {
	// カスタムフィールド名が「_events_extra」の場合、接頭辞の「_events_」を除いた値を指定する
	$meta_keys[] = 'extra';

	return $meta_keys;
} );
```

上記の例は、日付グループの下部に表示したいので「taro_events_bottom_metabox_basic_group」というフックを使いました。

他の位置に表示したい場合は、それぞれ下記のフックに置き換えてください。

- 基本
    - `taro_events_before_metabox_basic_group` 基本グループの先頭（見出しの下）
    - `taro_events_top_metabox_basic_group` 基本グループの先頭フィールド
    - `taro_events_bottom_metabox_basic_group` 基本グループの最後フィールド
    - `taro_events_after_metabox_basic_group` 基本グループの最後尾（table の下）
- 日付
    - `taro_events_before_metabox_date_group` 日付グループの先頭（見出しの下）
    - `taro_events_top_metabox_date_group` 日付グループの先頭フィールド
    - `taro_events_bottom_metabox_date_group` 日付グループの最後フィールド
    - `taro_events_after_metabox_date_group` 日付グループの最後尾（table の下）
- 場所
    - `taro_events_before_metabox_location_group` 場所グループの先頭（見出しの下）
    - `taro_events_top_metabox_location_group` 場所グループの先頭フィールド
    - `taro_events_bottom_metabox_location_group` 場所グループの最後フィールド
    - `taro_events_after_metabox_location_group` 場所グループの最後尾（table の下）
- 申し込み
    - `taro_events_before_metabox_offers_group` 申し込みグループの先頭（見出しの下）
    - `taro_events_top_metabox_offers_group` 申し込みグループの先頭フィールド
    - `taro_events_bottom_metabox_offers_group` 申し込みグループの最後フィールド
    - `taro_events_after_metabox_offers_group` 申し込みグループの最後尾（table の下）
- 主催者
    - `taro_events_before_metabox_organizer_group` 主催者グループの先頭（見出しの下）
    - `taro_events_top_metabox_organizer_group` 主催者グループの先頭フィールド
    - `taro_events_bottom_metabox_organizer_group` 主催者グループの最後フィールド
    - `taro_events_after_metabox_organizer_group` 主催者グループの最後尾（table の下）


### 絞り込みフォームの選択項目を非表示にする。

絞り込みフォームの選択項目のうち使わないものを非表示にしたい場合、下記のようなフックを記述します。

```
// 絞り込みからイベントカテゴリーを非表示にする。
add_filter( 'taro_events_is_available_filter_event_category', function() {
    return false;
} ); 

// 絞り込みからイベント種別を非表示にする。
add_filter( 'taro_events_is_available_filter_event_type', function() {
    return false;
} ); 

// 絞り込みからイベントステータスを非表示にする。
add_filter( 'taro_events_is_available_filter_event_status', function() {
    return false;
} ); 
```


### 絞り込みフォームに選択項目を追加する

絞り込みフォームに新たな選択項目を追加する方法です。

以下、「オンラインイベントかどうか」「オフラインイベントかどうか」の選択肢を加えるサンプルとなります。

#### （1）クエリ変数の追加

```
/**
 * オンラインorオフラインの検索フィールド用変数を追加
 */
function add_event_format_query_var( $vars ) {
    // ここでは変数名を「event-format」とします。
    $vars[] = 'event-format';

    return $vars;
}
add_filter( 'query_vars', 'add_event_format_query_var' );
```

#### （2）絞り込みフォームの項目を追加

```
/**
 * オンラインorオフラインのフォーム項目追加
 */
function add_event_format_field( $form, $form_open, $form_event_category, $form_event_type, $form_event_status, $form_submit, $form_close ) {
    $event_format = get_query_var( 'event-format' );

    // 追加する選択項目のhtml作成
    $add_field = '<div class="event-format-label">イベント形式<div>';
    $add_field .= '<select name="event-format" id="event-format">';
    $add_field .= '<option value="">指定しない</option>';
    $add_field .= '<option value="offline"' . selected( ( 'offline' === $event_format ), true, false ) . '>オフライン</option>';
    $add_field .= '<option value="online"' . selected( ( 'online' === $event_format ), true, false ) . '>オンライン</option>';
    $add_field .= '</select>';
    $add_field .= '</div>';

    return $form_open . $form_event_category . $form_event_type . $form_event_status . $add_field . $form_submit . $form_close;
}
add_filter( 'taro_events_get_filter_form_html', 'add_event_format_field', 10, 7 );
```

#### （3）検索処理を追加

```
/**
 * オンラインorオフラインの検索絞り込みクエリ追加
 */
function add_event_format_filter_query( $wp_query ) {
    if ( is_admin() || ! $wp_query->is_main_query() ) {
        return;
    }

    // イベントアーカイブの時だけ絞り込みクエリを追加する
    if ( $wp_query->is_post_type_archive( taro_events_post_type() ) ) {

        // オンラインorオフラインの選択値を取得
        $event_format = get_query_var( 'event-format' );

        // オフライン選択時
        if ( 'offline' === $event_format ) {
            $wp_query->set( 'meta_query', array(
                array(
                    'key'     => taro_events_meta_prefix() . 'is_offline',
                    'value'   => '1',
                    'compare' => '=',
                )
            ) );

        // オンライン選択時
        } elseif ( 'online' === $event_format ) {
            $wp_query->set( 'meta_query', array(
                array(
                    'key'     => taro_events_meta_prefix() . 'is_online',
                    'value'   => '1',
                    'compare' => '=',
                )
            ) );
        }
    }
}
add_filter( 'pre_get_posts', 'add_event_format_filter_query', 20 );
```

上記のコードを加えることで、「オフライン」「オンライン」のセレクトボックスが絞り込みフォームに追加されます。


### RSS フィードにカスタムフィールドの出力を追加する

RSS フィードにカスタムフィールドの出力を追加する方法です。

以下、3つの項目を追加する時のサンプルとなります。

- 主催者 (イベントカテゴリ)（`<ex:category>`）
- 受付ステータス（1:受付中、0:受付中でない）（`<ex:is_accepting>`）
- 開催ステータス（1:開催中、0:開催中でない）（`<ex:is_opening>`）


```
/**
 * カスタムnamespaceを追加
 */
add_action( 'rss2_ns', function () {
	if ( ! ( is_feed( 'rss2' ) && taro_events_post_type() === get_post_type() ) ) {
		return;
	}

	// 下記 namespace の設定値は、導入するサイトに合わせて変更することをお勧めします。
	echo 'xmlns:ex="http://example.jp/rss/1.0/modules/event"' . "\n";
}, 20 );

/**
 * 出力したい要素をRSSに追加
 */
add_action( 'rss2_item', function () {
	if ( ! ( is_feed( 'rss2' ) && taro_events_post_type() === get_post_type() ) ) {
		return;
	}

	// カテゴリ
	$terms = get_the_terms( get_the_ID(), taro_events_taxonomy_event_category() );
	if ( $terms ) {
		$term_names = [];
		foreach ( $terms as $term ) {
			$term_names[] = $term->name;
		}
		if ( ! empty( $term_names ) ) {
			echo '<ex:category><![CDATA[' . esc_html( implode( ',', $term_names ) ) . "]]></ex:category>\n";
		}
	}

	// 受付ステータス
	$accepting = taro_events_is_event_accepting() ? '1' : '0';
	echo '<ex:is_accepting>' . esc_html( $accepting ) . "</ex:is_accepting>\n";

	// 開催ステータス
	$opening = taro_events_is_event_opening() ? '1' : '0';
	echo '<ex:is_opening>' . esc_html( $opening ) . "</ex:is_opening>\n";
}, 20 );
```

RSS 2.0 の仕様に沿う形で任意項目を追加するには、namespace を `<rss>` タグの属性に定義し、定義した要素名を使ってタグを追加する必要があります。

上記サンプルで言うと、`xmlns:ex="http://example.jp/rss/1.0/modules/event"` が namespage で、そこで定義された `ex` を使って `<ex:category>・・・</ex:category>` タグを出力するようにします。

- 参考：https://validator.w3.org/feed/docs/rss2.html
    - `"RSS 2.0 adds that capability, following a simple rule. A RSS feed may contain elements and attributes not described on this page, only if those elements and attributes are defined in a namespace."`

### RSS を受信して表示する

（このプラグインの範疇外ですが）イベント情報の RSS を受信して、ページに表示する方法です。

以下、WordPress標準の `fetch_feed` 関数を使って受信する例となります。


```
        <h2>RSS表示サンプル</h2>
		<?php
			// 取得するRSSフィードURL
			$feed_url = 'https://example.com/event/feed/';

			// namespace（変更不要）
			$namespace_event_ev = 'http://purl.org/rss/1.0/modules/event/';

			// RSSを取得
            $rss = fetch_feed( $feed_url );

			if ( ! is_wp_error( $rss ) ) :
				// RSS件数を取得
				$item_quantity = $rss->get_item_quantity();
				// 引数を指定すると表示したい件数だけを取得
				//$item_quantity = $rss->get_item_quantity( 5 );

				// 取得したitemを配列にセット
				$rss_items = $rss->get_items( 0, $item_quantity );

				// 取得件数が0件の場合
				if ( ! $item_quantity ) :
				?>
					<p>データは0件です。</p>

				<?php
				// 取得件数が1件以上ある場合
				else :
					foreach( $rss_items as $item ) :
						?>
						<ul>
							<li>タイトル：<?php echo esc_html( $item->get_title() ); ?></li>
							<li>URL：<?php echo esc_url( $item->get_permalink() ); ?></li>
							<li>投稿日時：<?php echo esc_html( wp_date( 'Y-m-d H:i:s', $item->get_date( 'U' ) ) ); ?></li>
							<li>説明（抜粋）：<?php echo esc_html( $item->get_description() ); ?></li>
							<?php
							// アイキャッチ画像がある場合
							if ( $enclosure = $item->get_enclosure() ) :
								?>
								<li>アイキャッチ画像：<?php echo $enclosure->get_link(); ?></li>
								<?php
							endif;
							?>
							<li>開始日：<?php echo wp_date( 'Y-m-d H:i:s', esc_html( strtotime( $item->data['child'][$namespace_event_ev]['startdate'][0]['data'] ) ) ); ?></li>
							<li>終了日：<?php echo wp_date( 'Y-m-d H:i:s', esc_html( strtotime( $item->data['child'][$namespace_event_ev]['enddate'][0]['data'] ) ) ); ?></li>
							<li>場所：<?php echo esc_html( $item->data['child'][$namespace_event_ev]['location'][0]['data'] ); ?></li>
							<li>主催者：<?php echo esc_html( $item->data['child'][$namespace_event_ev]['organizer'][0]['data'] ); ?></li>
							<li>タイプ：<?php echo esc_html( $item->data['child'][$namespace_event_ev]['type'][0]['data'] ); ?></li>
						</ul>
						<?php
					endforeach;
				endif;

			else :
				echo sprintf( '<p>RSSが取得できませんでした。（:%s）</p>', $rss->get_error_message() );
			endif;
		?>
```

#### 検証用 Basic認証付きサイトの RSS を受信して表示する

ステージング環境など Basic 認証付きサイトから RSS フィードを受信しようとした場合、
`https://username:password@example.com` のような ID/PW 入りの URL を指定したいケースがありますが、
その URL を指定すると `fetch_feed` 関数が「無効なURLです」エラーを出し動作しません。

[taro-can-fetch-authorized-feed.zip](https://github.com/tarosky/taro-events/files/8002415/taro-can-fetch-authorized-feed.zip) プラグインを RSS 受信する側のサイトにインストール〜有効化すると、
そのエラーを回避できるようになりますので、ID/PW 入りの URL でも検証することができます。

※あくまでBasic認証がある検証環境用のプラグインですので、本番環境には入れないようにしてください。


## 貢献

気付いた事があれば [issue](https://github.com/tarosky/taro-events/issues) に登録してください。[pull requests](https://github.com/tarosky/taro-events/pulls) もお待ちしています。


## 更新履歴

### 1.0.7

* Improve README
* Fix bugs

### 1.0.6

* Add hooks
* Change custom fields
* Fix bugs

### 1.0.5

* Add suupport for Japanese
* Add custom fields
* Fix bugs

### 1.0.4

* Improve RSS feed

### 1.0.2

* Add functions
* Improve structured data
* Improve hooks
* Fix bugs

### 1.0.1

* Add custom fields
* Improve structured data
* Fix bugs

### 1.0.0

* First release.
