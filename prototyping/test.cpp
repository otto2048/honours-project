#include <iostream>

int main() {
    int a = 0;
    int b = 0;
    int c = 0;

    std::cout << "Addition app" << std::endl;
    std::cout << "Enter first number: ";
    std::cin >> a;
    std::cout << "Enter second number: ";
    std::cin >> b;

    c = a + b;

    std::cout << a << " + " << b << " = " << c << std::endl;

    return 0;
}