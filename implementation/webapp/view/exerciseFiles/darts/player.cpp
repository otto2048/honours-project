#include "player.h"
#include &lt;string&gt;
#include &lt;iostream&gt;
using namespace std;

player::player(string n, int s, int p)
{
	name = n;
	successRate = s;
	score = p;

	for (int i = 0; i < 3; i++)
	{
		set[i] = 0; //initialize last three throws array
	}

	for (int i = 0; i < 3; i++)
	{
		winStrategy[i] = 0; //initialize array
	}
}

player::~player()
{
	cout << name << " object destroyed";
}

string player::getName()
{
	return name;
}

int player::getSuccessRate()
{
	return successRate;
}

int player::getThrows()
{
	return throws;
}

void player::setThrows(int i)
{
	throws = i;
}

int player::getGamesWon()
{
	return gamesWon;
}

void player::setGamesWon(int i)
{
	gamesWon = i;
}

int player::getScore()
{
	return score;
}

void player::setScore(int i)
{
	score = i;
}

int player::getSetsWon()
{
	return setsWon;
}

void player::setSetsWon(int i)
{
	setsWon = i;
}

void player::savingSet(int position, int score)
{
	set[position] = score;
}

int player::getSet(int position)
{
	return set[position];
}

int player::standardThrow(int c, int p) //a standard throw where the resulting score has no impact
{
	savingSet(c, p); //each throw is saved incase score goes below 2
	c = throws + 1;
	score = (score - p);
	throws = c; //increase throws
	return c; //return counter as its not a variable associated with the player object
}

void player::setWinStrategy(int position, int score)
{
	winStrategy[position] = score;
}

int player::getWinStrategy(int position)
{
	return winStrategy[position];
}