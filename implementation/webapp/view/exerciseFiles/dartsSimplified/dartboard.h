#pragma once
class dartboard
{
private:
	int db[2][21] = { {0,20,15,17,18,12,13,19,16,14,6,8,9,4,11,10,7,2,1,3,5},
			   {0,18,17,19,13,20,10,16,11,12,15,14,5,6,9,2,8,3,4,7,1} };
	int doubleValue[20];
	int trebleValue[20];

	int threeThrowFinish[4][103]; //storing all possible three throw finishes
	int twoThrowFinish[3][82]; //storing all possible two throw finishes

public:
	dartboard();
	~dartboard();

	int getBoardValue(int, int);

	void setDoubleValues(int, int);
	int getDoubleValues(int);

	void setTrebleValues(int, int);
	int getTrebleValues(int);

	int getThreeThrowFinish(int, int);

	int getTwoThrowFinish(int, int);
};

