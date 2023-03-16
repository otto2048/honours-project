#pragma once
#include "Vehicle.h"
class Lorry : public Vehicle
{
private:
	int wheels;
	float haulage;

public:
	Lorry();
	Lorry(int, string, string, int, float, float, float, int, float);

	float getHaulage();
	int getWheels();
};

