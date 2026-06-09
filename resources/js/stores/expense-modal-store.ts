import { create } from "zustand";

type ExpenseModalStore = {
    open: boolean;
    handleToogleModal: () => void;
};

export const useExpenseModalStore = create<ExpenseModalStore>((set, get) => ({
    open: false,

    handleToogleModal: () => {
        set({
            open: !get().open,
        });
    },
}));
