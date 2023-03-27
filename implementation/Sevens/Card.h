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

	//function that lets us compare two Card objects
	bool operator==(const Card& obj);
};

