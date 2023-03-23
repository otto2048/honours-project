#include "MySevensGame.h"

bool MySevensGame::checkIfCardHeld(Card* card)
{
	// check if either player holds this card
	for (int i = 0; i < Player::numCards; i++)
	{
		if (players[0].getCard(i) == card || players[0].getCard(i) == card)
		{
			return true;
		}
	}

	return false;
}



void MySevensGame::initPlayerCards(int playerId)
{
	int cardsGiven = 0;
	while (cardsGiven < 7)
	{
		// get random card
		int index = rand() % MySevensGame::numCards;

		Card* card = cards[index];

		// check if this card is already held
		bool holdsCard = checkIfCardHeld(card);

		// if card isn't held, add to players hand
		if (!holdsCard)
		{
			players[playerId].setCard(card, cardsGiven);
			cardsGiven++;
		}
	}
}