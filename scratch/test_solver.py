import sys, os
import json
import random

# Test if we can generate items for a target (omzet, qty) using actual prices
# Let's say available prices for a wilayah
# Let's see all unique prices for Wilayah 1, 2, 3
prices_w1 = [28500, 35000, 48000, 51500, 55000, 65000, 70000, 72500, 80000, 85000]

def solve_basket(target_omzet, target_qty, prices):
    """
    Find integer quantities q_i >= 0 such that:
    sum(q_i) = target_qty
    sum(q_i * p_i) = target_omzet
    """
    # Using dynamic programming or randomized search / greedy + adjustment
    # Since prices are multiples of 500, target_omzet is also multiple of 500.
    # Let's test a simple ILP or greedy solver
    from scipy.optimize import milp, LinearConstraint, Bounds
    import numpy as np

    c = np.ones(len(prices)) # dummy objective
    # constraints:
    # 1: sum(q) = target_qty
    # 2: sum(q * p) = target_omzet
    A = np.array([
        np.ones(len(prices)),
        prices
    ])
    b_l = np.array([target_qty, target_omzet])
    b_u = np.array([target_qty, target_omzet])
    constraints = LinearConstraint(A, b_l, b_u)
    integrality = np.ones(len(prices))
    bounds = Bounds(0, target_qty)

    res = milp(c=c, integrality=integrality, constraints=constraints, bounds=bounds)
    if res.success:
        return [int(round(x)) for x in res.x]
    return None

# Test with target: 18,900,000 and 415 qty
sol = solve_basket(18900000, 415, prices_w1)
print("Solution found:", sol)
if sol:
    total_q = sum(sol)
    total_cost = sum(q * p for q, p in zip(sol, prices_w1))
    print(f"Total Q: {total_q}, Total Cost: {total_cost}")
