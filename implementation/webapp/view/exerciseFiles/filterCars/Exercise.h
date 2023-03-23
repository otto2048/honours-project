#pragma once
#include "Showroom.h"

//extend the Showroom class
class My_Showroom : public Showroom
{
public:
	Car* filterCars(int);
};