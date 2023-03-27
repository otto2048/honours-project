#include "MySevensGame.h"

void MySevensGame::shuffleCards()
{
	for (int i = 0; i < 100; i++)
	{
		int cardOneIndex = rand() % MySevensGame::numCards;

		int cardTwoIndex = rand() % MySevensGame::numCards;

		std::swap(fullDeck[cardOneIndex], fullDeck[cardTwoIndex]);
	}
}

void MySevensGame::initCards()
{
	// get player cards
	for (int i = 0; i < 14; i++)
	{
		if (i % 2 == 0)
		{
			players[0].setCard(fullDeck[i], i / 2);
		}
		else
		{
			players[1].setCard(fullDeck[i], (i - 1) / 2);
		}
	}

	// create stack of cards
	int counter = 0;
	for (int i = 15; i < numCards; i++)
	{
		stack[counter] = fullDeck[i];
		counter++;
	}
}

Card* MySevensGame::pickHiddenCard(int playerId)
{
	Card* newCard;

	cout << "Player takes new card: ";

	// picking up a new card from the hidden pile
	newCard = &stack[switchPoint];

	cout << newCard->getSuit() << " " << newCard->getValue() << endl;

	switchPoint++;

	if (switchPoint >= numStackCards)
	{
		//get new switch point
		switchPoint = 0;
	}

	return newCard;
}

Card* MySevensGame::pickVisibleCard(int playerId)
{
	cout << "Player attempts to take visible card: ";

	int visCardIndex = switchPoint - 1;

	if (visCardIndex >= 0)
	{
		return &stack[visCardIndex];
	}
	else
	{
		return pickHiddenCard(playerId);
	}
}

void MySevensGame::swapCard(Card* newCard, int playerId)
{
	// swap new card with worst card from our hand
	int worstCardIndex = players[playerId].getReplacementCard(*newCard);

	// if the worst card isnt the new card
	if (worstCardIndex > -1)
	{
		Card playerCard = players[playerId].getCard(worstCardIndex);
		Card stackCard = *newCard;

		cout << "Player places down: " << playerCard.getSuit() << " " << playerCard.getValue() << endl;
		cout << endl;
		cout << endl;

		*newCard = playerCard;
		players[playerId].setCard(stackCard, worstCardIndex);
	}
	else
	{
		cout << "The worst card was the one we picked up" << endl;
		cout << "Player places down: " << newCard->getSuit() << " " << newCard->getValue() << endl;
		cout << endl;
		cout << endl;
	}
}

bool MySevensGame::numberEqualityWinCondition(Card cards[], int numCards)
{
	bool ret = true;
	int cardValue = cards[0].getValue();

	for (int i = 0; i < numCards; i++)
	{
		if (cards[i].getValue() != cardValue)
		{
			ret = false;
			break;
		}
	}

	return ret;
}

bool MySevensGame::sequenceEqualityWinCondition(Card cards[], int numCards)
{
	// sort the cards
	bubbleSort(cards, numCards);

	bool ret = true;

	int initSuit = cards[0].getSuit();

	// check if the cards increase in value by one each card and cards are all the same suit
	for (int i = 1; i < numCards; i++)
	{
		if (cards[i].getValue() - cards[i - 1].getValue() != 1 || cards[i].getSuit() != initSuit)
		{
			ret = false;
			break;
		}
	}

	return ret;
}

void MySevensGame::bubbleSort(Card cards[], int numCards)
{
	for (int step = 0; step < numCards - 1; step++)
	{
		//check if swapping has occured
		int swapped = 0;

		for (int i = 0; i < (numCards - step - 1); i++)
		{
			//compare two elements
			if (cards[i].getValue() > cards[i + 1].getValue())
			{
				//swap elements
				std::swap(cards[i], cards[i + 1]);

				swapped = 1;
			}
		}

		if (swapped == 0)
		{
			//no swapping occured, array is sorted, break out of loop
			break;
		}
	}
}