# ■要件　カレンダー

- 期間：1980年1月1日～ 2024年12月31日
- 祝日法：表示年に合わせた祝日法を使用
- 祝日：外部ファイルに定義。
固定の祝日のみDefineしてもよい。それ以外の祝日は動的に生成すること。
外部定義ファイルのフォーマットについては応相談。
- デザイン：好きに
<details>

# ■祝日には幾つかのパターンがある
- 固定の日付(元旦、憲法記念日など)
- 移動祝日(「成人の日」(1月の第2月曜)、「体育の日」(10月の第2月曜)など)
- 計算式で決まるもの(春分の日、秋分の日)<-略式でいいので計算で算出すること。
これらをどのように定義ファイルに書くか。

# ■祝日に関して注意点
- 名前が変わった祝日(「天皇誕生日」->「みどりの日」など)
- 日付が変わった祝日(天皇誕生日12月23日から2月23日に変更)
- 振替休日のルールも2007年に変更になっている


![image](https://github.com/user-attachments/assets/6a88d153-6319-4bfe-ad19-b9a3401a038b)
![image](https://github.com/user-attachments/assets/9536ffad-e8ed-4d67-a334-402042091279)

	テスト項目	対象		

![image](https://github.com/user-attachments/assets/db4809f3-f91d-43ae-9b02-4b71de3f9dc5)


完成形
![image](https://github.com/user-attachments/assets/27193f63-f1bb-4969-9cc7-f3fb34c69bb6)
</details>


# 掲示板作成中
■要件
Ver.1.0からの追加機能
- ユーザー登録　◯　完了 名前、ログインID(メールアドレスでも可)、パスワード。パスワードはDBに保存するときは暗号化すること。(ハッシュとかで良い)
- ログイン　◯　完了（ホワイトアウトバグ調査中）管理者と一般のユーザー権限がある。管理者はすべての書き込みに対し編集と削除が行える。
- スレッド表示　◯　親記事を一番上に表示しインデントを付けてレスポンスを表示する。
- レスポンス機能(返信)　◯　親記事に対してレスポンスを付けられる。レスポンスのレスポンスは出来ない。
- 書き込み編集　◯　自分の書き込みもしくは管理者のみが編集が出来る。　（現仕様は誰でも編集ができる）
- 書き込み削除　◯　自分の書き込みもしくは管理者のみが削除できる。削除した場合それに紐付いていたレスポンスは非表示になる。（管理者のみ）
- 工夫した点　javascriptでフラッシュメッセージを出現させる。ログイン時パスワードを見える見えないを切り替えできるように。
<details>
![image](https://github.com/user-attachments/assets/1922196d-2627-426b-96fe-716c1a1f8cf9)
![image](https://github.com/user-attachments/assets/a35309f0-fdff-4a34-bfd9-053af54857e9)
![image](https://github.com/user-attachments/assets/843fe5f3-8707-47bb-aaad-f7db940ad6f4)
![image](https://github.com/user-attachments/assets/7e28f040-d2a3-44e4-8204-598c9a1992bd)


</details>

<details>
	![image](https://github.com/user-attachments/assets/069f8789-29fd-40a4-a3e3-66165505607a)
	![圧縮アルゴリズム](https://github.com/user-attachments/assets/d13f4f05-e664-402e-a749-5580ae0e410c)

</details>
