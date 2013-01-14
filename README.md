#会員属性を指定して表示変更

EC-CUBE plugin.  
※ Make sure to add tests for it. 
  This is important so I don't break it in a future version unintentionally.  
※ Sorry, but currently Japanese only.  

指定した属性を持つ会員に対して表示を変更できます。  
特定の会員に対してメッセージを表示したり、あるいはA/Bテストにも利用できます。  

###使い方

・プラグインを有効にすると、会員検索後に「検索条件を保存」「保存した条件で検索」ボタンが表示されます。  

・条件に合致する会員からのアクセスの場合、
テンプレート変数 $c4c にtrueが入るので下記のように利用してください。  

    <!--{if $c4c}-->
        ----  メッセージ  ----
    <!--{if}-->    

・保存した条件を確認するには、「保存した条件で検索」ボタンを使ってください。  

