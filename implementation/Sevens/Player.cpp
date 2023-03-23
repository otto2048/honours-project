#include "Player.h"

Player::Player()
{
	score = 0;
}

Player::Player(int id_)
{
	id = id_;
}

Card* Player::getCard(int pos)
{
	return cards[pos];
}

void Player::setCard(Card* card, int pos)
{
	cards[pos] = card;
}