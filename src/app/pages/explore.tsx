import * as React from "react";
import { 
  Laptop, Music, Sparkles, Code2, TrendingUp, Clock, ArrowRight,
  FlaskConical, Dumbbell, Wallet, HeartPulse, Plane, Utensils, Film, Palette
} from "lucide-react";
import { cn } from "../components/ui/utils";

// 🎨 DEV DİNAMİK TEMA MOTORU: 12 Farklı Kategori ve Renk Aurası
const CATEGORY_THEMES = {
  yazilim: { icon: Code2, label: "Yazılım", colors: { bg: "bg-teal-50 dark:bg-teal-900/20", text: "text-teal-600 dark:text-teal-400", border: "border-teal-200 dark:border-teal-800", glow: "bg-teal-500/10", hover: "hover:border-teal-400 dark:hover:border-teal-500" } },
  teknoloji: { icon: Laptop, label: "Teknoloji", colors: { bg: "bg-blue-50 dark:bg-blue-900/20", text: "text-blue-600 dark:text-blue-400", border: "border-blue-200 dark:border-blue-800", glow: "bg-blue-500/10", hover: "hover:border-blue-400 dark:hover:border-blue-500" } },
  bilim: { icon: FlaskConical, label: "Bilim", colors: { bg: "bg-cyan-50 dark:bg-cyan-900/20", text: "text-cyan-600 dark:text-cyan-400", border: "border-cyan-200 dark:border-cyan-800", glow: "bg-cyan-500/10", hover: "hover:border-cyan-400 dark:hover:border-cyan-500" } },
  finans: { icon: Wallet, label: "Finans & Ekonomi", colors: { bg: "bg-emerald-50 dark:bg-emerald-900/20", text: "text-emerald-600 dark:text-emerald-400", border: "border-emerald-200 dark:border-emerald-800", glow: "bg-emerald-500/10", hover: "hover:border-emerald-400 dark:hover:border-emerald-500" } },
  saglik: { icon: HeartPulse, label: "Sağlık & Yaşam", colors: { bg: "bg-red-50 dark:bg-red-900/20", text: "text-red-600 dark:text-red-400", border: "border-red-200 dark:border-red-800", glow: "bg-red-500/10", hover: "hover:border-red-400 dark:hover:border-red-500" } },
  spor: { icon: Dumbbell, label: "Spor", colors: { bg: "bg-orange-50 dark:bg-orange-900/20", text: "text-orange-600 dark:text-orange-400", border: "border-orange-200 dark:border-orange-800", glow: "bg-orange-500/10", hover: "hover:border-orange-400 dark:hover:border-orange-500" } },
  yemek: { icon: Utensils, label: "Gastronomi", colors: { bg: "bg-amber-50 dark:bg-amber-900/20", text: "text-amber-600 dark:text-amber-400", border: "border-amber-200 dark:border-amber-800", glow: "bg-amber-500/10", hover: "hover:border-amber-400 dark:hover:border-amber-500" } },
  seyahat: { icon: Plane, label: "Seyahat", colors: { bg: "bg-sky-50 dark:bg-sky-900/20", text: "text-sky-600 dark:text-sky-400", border: "border-sky-200 dark:border-sky-800", glow: "bg-sky-500/10", hover: "hover:border-sky-400 dark:hover:border-sky-500" } },
  sinema: { icon: Film, label: "Sinema", colors: { bg: "bg-indigo-50 dark:bg-indigo-900/20", text: "text-indigo-600 dark:text-indigo-400", border: "border-indigo-200 dark:border-indigo-800", glow: "bg-indigo-500/10", hover: "hover:border-indigo-400 dark:hover:border-indigo-500" } },
  muzik: { icon: Music, label: "Müzik", colors: { bg: "bg-purple-50 dark:bg-purple-900/20", text: "text-purple-600 dark:text-purple-400", border: "border-purple-200 dark:border-purple-800", glow: "bg-purple-500/10", hover: "hover:border-purple-400 dark:hover:border-purple-500" } },
  sanat: { icon: Palette, label: "Sanat & Tasarım", colors: { bg: "bg-fuchsia-50 dark:bg-fuchsia-900/20", text: "text-fuchsia-600 dark:text-fuchsia-400", border: "border-fuchsia-200 dark:border-fuchsia-800", glow: "bg-fuchsia-500/10", hover: "hover:border-fuchsia-400 dark:hover:border-fuchsia-500" } },
  moda: { icon: Sparkles, label: "Moda", colors: { bg: "bg-rose-50 dark:bg-rose-900/20", text: "text-rose-600 dark:text-rose-400", border: "border-rose-200 dark:border-rose-800", glow: "bg-rose-500/10", hover: "hover:border-rose-400 dark:hover:border-rose-500" } }
};

type CategoryKey = keyof typeof CATEGORY_THEMES;

export function ExplorePage() {
  const [activeCat, setActiveCat] = React.useState<CategoryKey>("yazilim");
  const theme = CATEGORY_THEMES[activeCat].colors;
  const ActiveIcon = CATEGORY_THEMES[activeCat].icon;

  return (
    <div className="flex-1 min-h-screen bg-white dark:bg-slate-950 p-6 md:p-12 relative overflow-hidden transition-colors duration-500">
      
      {/* 🌟 Dinamik Arkaplan Parıltısı */}
      <div className={cn("absolute top-0 right-0 w-[40rem] h-[40rem] blur-[120px] rounded-full transition-colors duration-1000 -z-10", theme.glow)} />

      <div className="max-w-6xl mx-auto z-10 relative">
        
        <header className="mb-12">
          <h1 className="font-serif text-5xl italic tracking-tighter text-slate-900 dark:text-white mb-4">
            Keşfet
          </h1>
          <p className="text-slate-500 italic font-light">İlgini çeken dünyalara dal ve en çok okunanları yakala.</p>
        </header>

        {/* 🎛️ Kategori Filtreleri (Yatay Scroll ile çoklu kategori yönetimi) */}
        <div className="w-full overflow-x-auto pb-4 scrollbar-none mb-16 -mx-6 px-6 md:mx-0 md:px-0">
          <div className="flex w-max gap-3">
            {(Object.keys(CATEGORY_THEMES) as CategoryKey[]).map((key) => {
              const cat = CATEGORY_THEMES[key];
              const isActive = activeCat === key;
              return (
                <button
                  key={key}
                  onClick={() => setActiveCat(key)}
                  className={cn(
                    "flex items-center gap-2 px-5 py-3 rounded-full font-bold text-xs uppercase tracking-widest transition-all duration-300 border shrink-0",
                    isActive 
                      ? `${cat.colors.bg} ${cat.colors.text} ${cat.colors.border} shadow-lg scale-105` 
                      : "bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-500 hover:scale-105"
                  )}
                >
                  <cat.icon className="w-4 h-4" />
                  {cat.label}
                </button>
              );
            })}
          </div>
        </div>

        {/* 🏆 EN ÇOK OKUNANLAR VİTRİNİ */}
        <div className="mb-16">
          <div className="flex items-center justify-between mb-8">
            <h2 className="text-2xl font-serif italic text-slate-900 dark:text-white flex items-center gap-3 transition-colors duration-500">
              <TrendingUp className={cn("w-6 h-6", theme.text)} />
              En Çok Okunanlar
            </h2>
            <button className="text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
              Tümünü Gör
            </button>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {[1, 2, 3].map((item) => (
              <div 
                key={item} 
                className={cn(
                  "group p-6 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl cursor-pointer flex flex-col justify-between h-64",
                  theme.hover
                )}
              >
                <div>
                  <div className={cn("inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest mb-4 transition-colors duration-500", theme.bg, theme.text)}>
                    <ActiveIcon className="w-3 h-3 mr-1.5 inline" /> {CATEGORY_THEMES[activeCat].label}
                  </div>
                  <h3 className="text-lg font-bold text-slate-900 dark:text-white leading-snug group-hover:underline decoration-2 underline-offset-4 decoration-slate-300 dark:decoration-slate-700">
                    {activeCat === "finans" ? "Küresel Piyasalar ve Modern Yatırım Stratejileri" :
                     activeCat === "saglik" ? "Fonksiyonel Tıp ile Hücresel Yenilenme Rehberi" :
                     activeCat === "spor" ? "Veri Odaklı Antrenman: Vücut Sınırlarını Aşmak" :
                     "Seçili Kategoriye Göre Dinamik İçerik Başlığı Örneği"}
                  </h3>
                </div>
                
                <div className="flex items-center justify-between mt-6 pt-6 border-t border-slate-50 dark:border-slate-800/50">
                  <span className="text-xs text-slate-500 font-medium flex items-center gap-1">
                    <Clock className="w-3 h-3" /> 5 dk okuma
                  </span>
                  <div className={cn("w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 dark:bg-slate-800 group-hover:scale-110 transition-transform duration-300", theme.text)}>
                    <ArrowRight className="w-4 h-4" />
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>

      </div>
    </div>
  );
}