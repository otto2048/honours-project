#include "dartboard.h"
#include &lt;iostream&gt;
using namespace std;

dartboard::dartboard()
{
	//scores that can get out with three throws
	//initialising scores

	threeThrowFinish[0][0] = 170;
	threeThrowFinish[0][1] = 167;
	threeThrowFinish[0][2] = 164;
	threeThrowFinish[0][3] = 161;
	threeThrowFinish[0][4] = 160;

	int score = 158;
	for (int j = 5; j < 103; j++)
	{
		threeThrowFinish[0][j] = score;
		score = score--;
	}

	//initialising 1st dart

	threeThrowFinish[1][68] = 45; //T15

	threeThrowFinish[1][62] = 51; //T17

	threeThrowFinish[1][35] = 54; //T18
	threeThrowFinish[1][41] = 54;
	threeThrowFinish[1][59] = 54;

	//T19
	score = 57;
	for (int j = 32; j < 37; j = j + 2)
	{
		threeThrowFinish[1][j] = score;
	}

	threeThrowFinish[1][37] = score;
	threeThrowFinish[1][40] = score;
	threeThrowFinish[1][44] = score;
	threeThrowFinish[1][56] = score;
	threeThrowFinish[1][64] = score;

	//T20
	score = 60;
	for (int j = 0; j < 32; j++)
	{
		threeThrowFinish[1][j] = score;
	}

	threeThrowFinish[1][33] = score;
	threeThrowFinish[1][39] = score;
	threeThrowFinish[1][42] = score;
	threeThrowFinish[1][43] = score;

	for (int j = 45; j < 56; j++)
	{
		threeThrowFinish[1][j] = score;
	}

	threeThrowFinish[1][57] = score;
	threeThrowFinish[1][58] = score;
	threeThrowFinish[1][60] = score;
	threeThrowFinish[1][61] = score;

	//Outer bull
	threeThrowFinish[1][38] = 25;
	threeThrowFinish[1][63] = 25;
	threeThrowFinish[1][66] = 25;

	for (int i = 72; i < 83; i = i + 2)
	{
		threeThrowFinish[1][i] = 25;
	}

	//S20
	for (int i = 73; i < 80; i = i + 2)
	{
		threeThrowFinish[1][i] = 20;
	}
	threeThrowFinish[1][81] = 25; //82
	threeThrowFinish[1][77] = 18; //86
	threeThrowFinish[1][76] = 17; //87

	for (int i = 83; i < 103; i++)
	{
		threeThrowFinish[1][i] = (score = (threeThrowFinish[0][i] - 60));
	}

	score = 16;
	for (int j = 71; j > 66; j--)
	{
		threeThrowFinish[1][j] = score;
		score++;
	}

	threeThrowFinish[1][65] = 12;

	//initialising final dart

	//finish on bull
	score = 50;
	for (int j = 0; j < 4; j++)
	{
		threeThrowFinish[3][j] = score;
	}

	threeThrowFinish[3][8] = score;
	threeThrowFinish[3][33] = score;
	threeThrowFinish[3][41] = score;
	threeThrowFinish[3][63] = score;

	for (int j = 73; j < 80; j = j + 2)
	{
		threeThrowFinish[3][j] = score;
	}

	//finish on D20

	score = 40;
	threeThrowFinish[3][4] = score;
	threeThrowFinish[3][6] = score;
	threeThrowFinish[3][9] = score;
	threeThrowFinish[3][12] = score;
	threeThrowFinish[3][18] = score;
	threeThrowFinish[3][21] = score;
	threeThrowFinish[3][24] = score;
	threeThrowFinish[3][38] = score;
	threeThrowFinish[3][43] = score;

	for (int j = 45; j < 51; j++)
	{
		threeThrowFinish[3][j] = score;
	}
	threeThrowFinish[3][56] = score;
	threeThrowFinish[3][57] = score;
	threeThrowFinish[3][81] = score;
	threeThrowFinish[3][82] = score;

	for (int j = 74; j < 81; j = j + 2)
	{
		threeThrowFinish[3][j] = score;
	}

	for (int i = 83; i < 103; i++)
	{
		threeThrowFinish[3][i] = score;
	}

	//finish on D19

	threeThrowFinish[3][5] = 38;

	//finish on D18
	score = 36;
	for (int j = 7; j < 17; j = j + 3)
	{
		threeThrowFinish[3][j] = score;
	}

	threeThrowFinish[3][22] = score;

	//finish on D16

	score = 32;
	for (int j = 11; j < 21; j = j + 3)
	{
		threeThrowFinish[3][j] = score;
	}

	for (int j = 29; j < 36; j = j + 3)
	{
		threeThrowFinish[3][j] = score;
	}

	threeThrowFinish[3][39] = score;
	threeThrowFinish[3][44] = score;

	for (int j = 51; j < 56; j++)
	{
		threeThrowFinish[3][j] = score;
	}

	for (int j = 58; j < 63; j++)
	{
		threeThrowFinish[3][j] = score;
	}

	threeThrowFinish[3][64] = score;
	threeThrowFinish[3][68] = score;

	//finish on D15

	threeThrowFinish[3][72] = 30;

	//finish on D14
	threeThrowFinish[3][15] = 28;

	//finish on D12
	threeThrowFinish[3][19] = 24;

	score = 24;
	for (int j = 25; j < 35; j = j + 3)
	{
		threeThrowFinish[3][j] = score;
	}

	//finish on D10

	threeThrowFinish[3][23] = 20;
	threeThrowFinish[3][26] = 20;

	threeThrowFinish[3][40] = 18; //finish on D9

	//finish on D8

	threeThrowFinish[3][27] = 16;
	threeThrowFinish[3][30] = 16;
	threeThrowFinish[3][36] = 16;
	threeThrowFinish[3][42] = 16;

	for (int j = 67; j < 72; j++)
	{
		threeThrowFinish[3][j] = 16;
	}

	//finish on D7

	threeThrowFinish[3][65] = 14;

	//finish on D6
	threeThrowFinish[3][37] = 12;
	threeThrowFinish[3][66] = 12;

	//initialize second throw

	for (int j = 0; j < 103; j++)
	{
		score = threeThrowFinish[0][j] - threeThrowFinish[1][j] - threeThrowFinish[3][j];
		threeThrowFinish[2][j] = score;
	}

	//scores that can get out with two throws:
	//initialising scores

	twoThrowFinish[0][0] = 110;
	twoThrowFinish[0][1] = 107;
	twoThrowFinish[0][2] = 104;
	twoThrowFinish[0][3] = 101;
	twoThrowFinish[0][4] = 100;

	score = 98;
	for (int j = 5; j < 63; j++)
	{
		twoThrowFinish[0][j] = score;
		score--;
	}
	score = 39;
	for (int j = 63; j < 82; j++)
	{
		twoThrowFinish[0][j] = score;
		score = score - 2;
	}

	//initialise second throw
	//finish on D20
	twoThrowFinish[2][4] = 40;

	for (int j = 6; j < 13; j = j + 3)
	{
		twoThrowFinish[2][j] = 40;
	}

	for (int j = 9; j < 25; j = j + 3)
	{
		twoThrowFinish[2][j] = 40;
	}

	for (int j = 43; j < 54; j++)
	{
		twoThrowFinish[2][j] = 40;
	}
	twoThrowFinish[2][57] = 40; //46
	twoThrowFinish[2][33] = 40; //70


	//finish on D19
	twoThrowFinish[2][8] = 38;
	twoThrowFinish[2][5] = 38;

	//finish on D18
	for (int j = 7; j < 17; j = j + 3)
	{
		twoThrowFinish[2][j] = 36;
	}
	twoThrowFinish[2][22] = 36;
	twoThrowFinish[2][37] = 36;

	//finish on D16
	for (int j = 11; j < 21; j = j + 3)
	{
		twoThrowFinish[2][j] = 32;
	}
	twoThrowFinish[2][29] = 32;
	twoThrowFinish[2][32] = 32;
	twoThrowFinish[2][41] = 32;

	for (int j = 54; j < 63; j++)
	{
		twoThrowFinish[2][j] = 32;
	}
	twoThrowFinish[2][57] = 40; //46

	//finish on D12
	twoThrowFinish[2][19] = 24;
	twoThrowFinish[2][25] = 24;
	twoThrowFinish[2][28] = 24;
	twoThrowFinish[2][31] = 24;
	twoThrowFinish[2][40] = 24;

	//finish on D10
	twoThrowFinish[2][23] = 20;
	twoThrowFinish[2][26] = 20;
	twoThrowFinish[2][38] = 20;

	//finish on D8

	for (int j = 27; j < 43; j = j + 3)
	{
		twoThrowFinish[2][j] = 16;
	}
	twoThrowFinish[2][33] = 40; //70

	twoThrowFinish[2][34] = 12; //finish on D6

	twoThrowFinish[2][35] = 8; //finish on D4

	twoThrowFinish[2][0] = 50; //finish on bull
	twoThrowFinish[2][3] = 50;
	twoThrowFinish[2][2] = 50;
	twoThrowFinish[2][1] = 50;

	//finish on a double
	score = 38;
	for (int j = 63; j < 82; j++)
	{
		twoThrowFinish[2][j] = score;
		score = score - 2;
	}

	//initialize first throw

	for (int j = 0; j < 82; j++)
	{
		score = twoThrowFinish[0][j] - twoThrowFinish[2][j];
		twoThrowFinish[1][j] = score;
	}

	/*for (int j = 0; j < 82; j++)
	{
		cout << twoThrowFinish[0][j] << " ";
		cout << twoThrowFinish[1][j] << " ";
		cout << twoThrowFinish[2][j] << " ";
	}

	for (int j = 0; j < 103; j++)
	{
		cout << threeThrowFinish[0][j] << " ";
		cout << threeThrowFinish[1][j] << " ";
		cout << threeThrowFinish[2][j] << " ";
		cout << threeThrowFinish[3][j] << " ";
	}*/
}

dartboard::~dartboard()
{

}


int dartboard::getBoardValue(int i, int j)
{
	return db[i][j];
}

void dartboard::setDoubleValues(int i, int a) //i=what is going in array, a is element
{
	doubleValue[a] = i;
}

int dartboard::getDoubleValues(int a)
{
	return doubleValue[a];
}

void dartboard::setTrebleValues(int i, int a) //i=what is going in array, a is element
{
	trebleValue[a] = i;
}

int dartboard::getTrebleValues(int a)
{
	return trebleValue[a];
}

int dartboard::getThreeThrowFinish(int a, int b)
{
	return threeThrowFinish[a][b];
}

int dartboard::getTwoThrowFinish(int a, int b)
{
	return twoThrowFinish[a][b];
}