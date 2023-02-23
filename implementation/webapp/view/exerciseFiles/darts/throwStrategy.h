#pragma once
class throwStrategy
{
public:
	int bullThrow(int); //parameter is success rate of player
	int singleThrow(int); //parameter is target of player
	int doubleThrow(int); //parameter is target of player
	int trebleThrow(int, int); //parameters are success rate of the player and target
};

