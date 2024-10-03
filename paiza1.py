# 入力値を読み込む
import sys

lines = sys.stdin.read().split()
N = int(lines[0])
c1 = int(lines[1])
c2 = int(lines[2])
prices = list(map(int, lines[3:]))

shares = 0    # 保有株数
cost = 0      # 購入にかかった総コスト
revenue = 0   # 売却による総収入

# 1 日目から N−1 日目までのシミュレーション
for i in range(N - 1):
    price = prices[i]
    if price <= c1:
        shares += 1
        cost += price
    elif price >= c2 and shares > 0:
        revenue += shares * price
        shares = 0

# N 日目に持ち株をすべて売却
price_N = prices[N - 1]
if shares > 0:
    revenue += shares * price_N
    shares = 0

# 最終的な損益を計算
profit = revenue - cost

# 結果を出力
print(profit)
