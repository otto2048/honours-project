#include "Card.h"

Card::Card()
{
	suit = -1;
	value = -1;
}

Card::Card(int suit_, int value_)
{
	suit = suit_;
	value = value_;
}

Card::~Card()
{

}

int Card::getSuit()
{
	return suit;
}

int Card::getValue()
{
	return value;
}

bool Card::operator==(const Card& obj)
{
	return (this->suit == obj.suit && this->value == obj.value);
}