#include "Player.h"

Player::Player()
{
	score = 0;
}

Player::Player(int id_)
{
	id = id_;
}

Card Player::getCard(int pos)
{
	return cards[pos];
}

//get position in our cards of the card we want to replace
//pass the new card that we will replace our worst card with
//our worst card will be the card with the highest value
int Player::getReplacementCard(Card& newCard)
{
	int worstValue = cards[0].getValue();
	int index = 0;

	for (int i = 0; i < Player::numCards; i++)
	{
		if (cards[i].getValue() > worstValue)
		{
			worstValue = cards[i].getValue();
			index = i;
		}
	}

	if (worstValue > newCard.getValue())
	{
		return index;
	}
	
	return -1;
}

int Player::getReplacementCardBugs(Card& newCard)
{
	int worstValue = cards[0].getValue();
	int index = 0;

	for (int i = 0; i < Player::numCards; i++)
	{
		if (cards[0].getValue() > worstValue)
		{
			worstValue = cards[i].getValue();
		}
	}

	if (worstValue > newCard.getValue())
	{
		return index;
	}

	return -1;
}

//set a card in the cards collection at a certain position
void Player::setCard(Card card, int pos)
{
	cards[pos] = card;
}