import * as React from "react";
import { Bookmark } from "lucide-react";
import { useState } from "react";
import { Button } from "./ui/button";
import { cn } from "./ui/utils"; // cn fonksiyonun varsa kullanmak için

export function BookmarkButton() {
  const [isSaved, setIsSaved] = useState(false);

  return (
    <Button
      variant="ghost"
      size="icon"
      onClick={(e) => {
        e.preventDefault(); // Kartın linkine gitmesini engeller
        setIsSaved(!isSaved);
      }}
      className={cn(
        "rounded-full transition-all duration-300 active:scale-75",
        isSaved 
          ? "text-teal-600 bg-teal-50/50 hover:bg-teal-50 shadow-sm" 
          : "text-slate-400 hover:text-teal-600 hover:bg-slate-50"
      )}
    >
      <Bookmark 
        className={cn(
          "h-5 w-5 transition-all",
          isSaved ? "fill-current scale-110" : "scale-100"
        )} 
      />
    </Button>
  );
}