#pragma once
#include &lt;string&gt;
using namespace std;
class player
{
private:
	//attributes
	string name;
	int successRate;
	int throws;
	int gamesWon;
	int setsWon;
	int score;
	int set[3];
	int winStrategy[3];

public:
	//functions
	player(string, int, int);
	~player();
	string getName();

	int getSuccessRate();

	int getThrows();
	void setThrows(int);

	int getGamesWon();
	void setGamesWon(int);

	int getSetsWon();
	void setSetsWon(int);

	int getScore();
	void setScore(int);

	void savingSet(int, int);
	int getSet(int);

	int standardThrow(int, int);

	void setWinStrategy(int, int);
	int getWinStrategy(int);
};

