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

void Player::setCard(Card card, int pos)
{
	cards[pos] = card;
}

//get the highest card
int Player::getWorstCard()
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

	return index;
}