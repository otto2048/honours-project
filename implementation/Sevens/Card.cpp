#include "Card.h"

Card::Card()
{
	suit = 0;
	value = 0;
}

Card::Card(int suit_, int value_)
{
	suit = suit_;
	value = value_;
}

Card::~Card()
{

}