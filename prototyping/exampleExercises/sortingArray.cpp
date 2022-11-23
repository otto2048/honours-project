// FIRST PART - STEPPING THROUGH ARRAY, SHOWING VARIABLES
#include <iostream>

using std::cout;
using std::endl;

int main()
{
    //defining an array
    const int arraySize = 5;

    int array[arraySize] = { 2, 4, 3, 1, 10};

    //looping through an array
    for (int i = 0; i < arraySize; i++)
    {
        // BREAKPOINT HERE - SHOW CONTENTS OF ARRAY AS YOU STEP THROUGH IT
        cout << "Element " << i << ": " << array[i] << endl;
    }
}

// SECOND PART, DEFINING A FUNCTION AND STEPPING INTO IT
#include <iostream>

using std::cout;
using std::endl;

//define element swapping function
void swapElement(int& a, int& b)
{
    //save value a
    int temp = a;

    //put value b in a
    a = b;

    //put temp value (a) in b
    b = temp;
}

int main()
{
    //defining an array
    const int arraySize = 5;

    int array[arraySize] = { 2, 4, 3, 1, 10};

    //looping through an array
    for (int i = 0; i < arraySize; i++)
    {
        cout << "Element " << i << ": " << array[i] << endl;
    }

    // BREAKPOINT HERE, STEP INTO SWAP FUNCTION, STEP THROUGH SWAP FUNCTION
    //swap elements 0 and 4
    swapElement(array[0], array[4]);

    // LOOK INTO ARRAY TO SEE IF VALUES HAVE SWAPPED

    return 0;
}

// THIRD PART, SORTING THE ARRAY WITH FUNCTION WE DEFINED
#include <iostream>

using std::cout;
using std::endl;

//define element swapping function
void swapElement(int& a, int& b)
{
    //save value a
    int temp = a;

    //put value b in a
    a = b;

    //put temp value (a) in b
    b = temp;
}

//define sorting function
void bubbleSort(int arr[], int arrSize)
{
    for (int step = 0; step < arrSize - 1; step++)
    {
        //check if swapping has occured
        int swapped = 0;

        for (int i = 0; i < (arrSize - step - 1); i++)
        {
            //compare two elements
            if (arr[i] > arr[i + 1])
            {
                //swap elements
                swapElement(arr[i], arr[i + 1]);

                swapped = 1;
            }
        }

        if (swapped == 0)
        {
            //no swapping occured, array is sorted, break out of loop
            break;
        }
    }
}

int main()
{
    //defining an array
    const int arraySize = 5;

    int array[arraySize] = { 2, 4, 3, 1, 10};

    //looping through an array
    for (int i = 0; i < arraySize; i++)
    {
        cout << "Element " << i << ": " << array[i] << endl;
    }

    // BREAKPOINT HERE, STEP INTO SWAP FUNCTION, STEP THROUGH SWAP FUNCTION
    //swap elements 0 and 4
    swapElement(array[0], array[4]);

    // LOOK INTO ARRAY TO SEE IF VALUES HAVE SWAPPED

    //sort array (ascending)

    // CHECK HOW MANY TIMES SWAPELEMENT WAS CALLED
    // USE VARIABLE INSPECTOR TO SEE IF ARRAY HAS SORTED
    bubbleSort(array, arraySize);

    for (int i = 0; i < arraySize; i++)
    {
        cout << "Element " << i << ": " << array[i] << endl;
    }

    return 0;
}