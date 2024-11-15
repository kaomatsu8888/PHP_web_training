def decorate_string(s):# 文字列を装飾する関数
    length = len(s) + 2 # 文字列の長さに装飾用の文字列を追加.paizaだったら+2
    border = "+" * length # 装飾用の文字列を作成.paizaだったら+2
    print(border) # 装飾用の文字列を出力.文字数+2
    print(f"+{s}+")# 文字列を出力.文字数+2
    print(border) # 文字数+2

# 入力例
s = input().strip() # 文字列を受け取る.strip()で前後の空白を削除
decorate_string(s) # 文字列を装飾する関数を呼び出す
