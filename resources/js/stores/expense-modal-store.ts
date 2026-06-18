import type { Budget } from "@/types/Budget";
import type { Category } from "@/types/Category";
import type { Expense } from "@/types/Expense";
import { create } from "zustand";
import { devtools } from "zustand/middleware";

type ExpenseModalStore = {
    open: boolean;
    budget: Budget | null;
    categories: Category[];
    expense: Expense | null;
    
    handleToogleModal: () => void;
    openEditModal: (expense: Expense) => void;
    setBudget: (budget: Budget) => void;
    setCategories: (categories: Category[]) => void;
    closeModal: () => void;
};

export const useExpenseModalStore = create<ExpenseModalStore>()(devtools((set, get) => ({
    open: false,
    budget: null,
    expense: null,
    categories: [],

    handleToogleModal: () => {
        set({
            open: !get().open,
        });
    },

    openEditModal: (expense) => {
        set({
            open: true,
            expense
        })
    },

    setBudget: (budget) => {
        set({
            budget,
        });
    },

    setCategories: (categories) => {
        set({
            categories,
        });
    },

    closeModal: () => set({
        open: false,
        expense: null
    }),
})));
