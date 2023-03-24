#include "MySevensGame.h"

void MySevensGame::shuffleCards()
{
	for (int i = 0; i < 100; i++)
	{
		int cardOneIndex = rand() % MySevensGame::numCards;

		int cardTwoIndex = rand() % MySevensGame::numCards;

		std::swap(cards[cardOneIndex], cards[cardTwoIndex]);
	}
}

bool MySevensGame::checkIfCardHeld(Card card)
{
	// check if either player holds this card
	for (int i = 0; i < Player::numCards; i++)
	{
		if (players[0].getCard(i) == card || players[1].getCard(i) == card)
		{
			return true;
		}
	}

	return false;
}

void MySevensGame::playTurn(bool pickHiddenCard, int swapChance, int playerId)
{
	int random = rand() % 100;
	Card newCard;

	if (pickHiddenCard)
	{
		// picking up a new card from the hidden pile
		newCard = cards[switchPoint];
	}
	else
	{
		// picking up the visible card
		int visCardIndex = getVisibleCard();

		if (visCardIndex >= 0)
		{
			newCard = cards[visCardIndex];
		}
		else
		{
			newCard = cards[switchPoint];
		}
	}

	switchPoint++;

	if (switchPoint >= numCards)
	{
		//get new switch point
		switchPoint = 0;

		Card hiddenCard = cards[switchPoint];

		while (checkIfCardHeld(hiddenCard))
		{
			switchPoint++;
			hiddenCard = cards[switchPoint];
		}
	}

	if (random < swapChance)
	{
		//swap the new card with the players highest card
		players[playerId].swapCards(newCard, players[playerId].getWorstCard());
	}
}

int MySevensGame::getVisibleCard()
{
	int lastVisCardIndex = switchPoint;

	bool cardHeld = true;
	Card visibleCard;

	while (cardHeld && lastVisCardIndex >= 0)
	{
		lastVisCardIndex--;

		if (lastVisCardIndex >= 0)
		{
			visibleCard = cards[lastVisCardIndex];
		}

		cardHeld = checkIfCardHeld(visibleCard);
	}

	if (!cardHeld)
	{
		return lastVisCardIndex;
	}
	else
	{
		return -1;
	}
}

void MySevensGame::initPlayerCards()
{
	for (int i = 0; i < 14; i++)
	{
		if (i % 2 == 0)
		{
			players[0].setCard(cards[i], i / 2);
		}
		else
		{
			players[1].setCard(cards[i], (i  - 1) / 2);
		}

		switchPoint++;
	}
}