
def dick(cards):
    arv = ['2','3','4','5','6','7','8','9','J','k','q']
    return sorted(cards, key=arv.index)

cards = ['Jack', 8, 2, 2, 6, 'King', 5, 3, 'Queen', 'King', 'Queen']
test = dick(cards)
print(test)