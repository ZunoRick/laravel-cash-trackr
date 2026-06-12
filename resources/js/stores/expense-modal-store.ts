import type { Budget } from "@/types/Budget";
import type { Category } from "@/types/Category";
import { create } from "zustand";

type ExpenseModalStore = {
    open: boolean;
    budget: Budget | null;
    categories: Category[];
    handleToogleModal: () => void;
    setBudget: (budget: Budget) => void;
    setCategories: (categories: Category[]) => void;
    closeModal: () => void;
};

export const useExpenseModalStore = create<ExpenseModalStore>((set, get) => ({
    open: false,
    budget: null,
    categories: [],

    handleToogleModal: () => {
        set({
            open: !get().open,
        });
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

    closeModal: () => set({ open: false }),
}));
