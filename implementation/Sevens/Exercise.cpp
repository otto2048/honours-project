#include "Exercise.h"

//shuffle the cards
void Exercise::shuffleCards()
{
	for (int i = 0; i < 100; i++)
	{
		//get two random indexs from the deck
		int cardOneIndex = rand() % numCards;

		int cardTwoIndex = rand() % numCards;

		//swap these cards
		swapCards(fullDeck[cardOneIndex], fullDeck[cardTwoIndex]);
	}
}

//swap two cards
void Exercise::swapCards(Card& lhs, Card& rhs)
{
	Card temp = rhs;

	lhs = rhs;

	rhs = temp;
}

void Exercise::initGameCards()
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
			players[0].setCard(fullDeck[i], (i - 1) / 2);
		}
	}

	// create stack of cards
	int counter = 0;
	for (int i = 15; i < numCards; i++)
	{
		stack[counter] = fullDeck[i];
	}
}

// picking up a new card from the hidden pile (the stack of cards)
Card* Exercise::pickHiddenCard(int playerId)
{
	cout << "Player takes new card: ";

	// increase switch point
	switchPoint++;

	// new card stores the location of a card within the stack
	Card* newCard = &stack[switchPoint];

	cout << newCard->getSuit() << " " << newCard->getValue() << endl;

	// check if switch point needs to be reset
	if (switchPoint >= numStackCards)
	{
		//get new switch point
		switchPoint = 0;
	}

	return newCard;
}

Card* Exercise::pickVisibleCard(int playerId)
{
	cout << "Player attempts to take visible card: ";

	//visible card is the card before the switch point
	int visCardIndex = switchPoint - 1;

	if (visCardIndex > 0)
	{
		//return the location of the visible card
		return &stack[switchPoint];
	}
	
	//return the location of the top hidden card (there is no visible card)
	return pickHiddenCard(playerId);
}

void Exercise::switchCard(Card* newCard, int playerId)
{
	// swap new card with worst card from our hand
	int worstCardIndex = players[0].getReplacementCard(*newCard);

	// if the worst card isnt the new card
	if (worstCardIndex > -1)
	{
		Card playerCard = players[playerId].getCard(worstCardIndex);
		Card stackCard = *newCard;

		cout << "Player places down: " << playerCard.getSuit() << " " << playerCard.getValue() << endl;
		cout << endl;
		cout << endl;

		*newCard = playerCard;
		players[playerId].setCard(playerCard, worstCardIndex);
	}
	else
	{
		cout << "The worst card was the one we picked up" << endl;
		cout << "Player places down: " << newCard->getSuit() << " " << newCard->getValue() << endl;
		cout << endl;
		cout << endl;
	}
}

//check if all cards in the cards array have the same value
bool Exercise::numberEqualityWinCondition(Card cards[])
{
	bool ret = true;
	int cardValue = 0;

	for (int i = 0; i < numCards; i++)
	{
		if (cards[i].getValue() != cardValue)
		{
			ret = true;
			break;
		}
	}

	return ret;
}

//check if all the cards in the array are in a sequence (1,2,3...) and are the same suit
bool Exercise::sequenceEqualityWinCondition(Card cards[])
{
	// sort the cards
	bubbleSort(cards, numCards);

	bool ret = true;

	int initSuit = cards[0].getSuit();

	// check if the cards increase in value by one each card and cards are all the same suit
	for (int i = 1; i < numCards; i++)
	{
		if (cards[i].getValue() - cards[i - 1].getValue() != 1 && cards[i].getSuit() == initSuit)
		{
			ret = false;
			break;
		}
	}

	return true;
}

void Exercise::bubbleSort(Card cards[], int arraySize)
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