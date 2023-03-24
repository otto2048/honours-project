#pragma once
class Card
{
private:
	int suit;
	int value;

public:
	Card(int, int);
	Card();
	~Card();

	int getSuit();
	int getValue();

	bool operator==(const Card& obj);
};

