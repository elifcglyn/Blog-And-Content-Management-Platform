import { useState } from "react";
import { Link } from "react-router";

import { Avatar, AvatarFallback, AvatarImage } from "../components/ui/avatar";
import { Button } from "../components/ui/button";
import { Badge } from "../components/ui/badge";
import { currentUser, mockPosts } from "../data/mock-data";
import { 
  Mail, Settings, Calendar, History, ArrowUpRight, 
  X, Camera, Check, Github, Globe, Twitter, UserPlus, Heart 
} from "lucide-react";

export function ProfilePage() {
  const [activeTab, setActiveTab] = useState("posts");
  
  // 🚀 MODAL STATE'LERİ
  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [isContactModalOpen, setIsContactModalOpen] = useState(false);

  // 🚀 ETKİLEŞİM STATE'LERİ (Takip ve Beğeni Simülasyonu)
  const [isFollowing, setIsFollowing] = useState(false);
  const [followerCount, setFollowerCount] = useState(1240);

  // 🚀 FORM STATE'LERİ
  const [userData, setUserData] = useState({
    name: currentUser.name,
    username: currentUser.username,
    bio: "Yazılım dünyasındaki karmaşık sistemleri, hikayeleştirerek ve versiyonlayarak anlatmayı seviyorum. Postify üzerinde teknoloji ve tasarım üzerine notlar paylaşıyorum.",
    avatar: currentUser.avatar
  });

  const handleSave = () => {
    setIsEditModalOpen(false);
    // Backend hazır olduğunda burada API isteği yapılacak
  };

  return (
    <div className="flex-1 flex flex-col bg-white dark:bg-slate-950 font-sans overflow-auto relative animate-in fade-in duration-500">
      
      
      <main className="flex-1 container mx-auto px-6 py-12 max-w-5xl">
        {/* PROFIL ÜST ALAN - EDITORIAL STYLE */}
        <section className="flex flex-col md:flex-row gap-10 items-center md:items-start mb-16 animate-in slide-in-from-bottom-6 duration-700">
          <div className="relative group">
            <div className="absolute -inset-2 bg-teal-500/20 rounded-full blur-xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
            <Avatar className="h-40 w-40 ring-4 ring-white dark:ring-slate-900 shadow-2xl relative">
              <AvatarImage src={userData.avatar} />
              <AvatarFallback className="text-4xl font-serif italic">{userData.name.charAt(0)}</AvatarFallback>
            </Avatar>
          </div>

          <div className="flex-1 text-center md:text-left space-y-4">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
              <div>
                <h1 className="font-serif text-5xl italic text-slate-900 dark:text-white tracking-tight leading-tight">
                  {userData.name}
                </h1>
                <p className="text-teal-600 font-medium tracking-widest uppercase text-xs mt-1">
                  @{userData.username} • Senior Technical Writer
                </p>
              </div>
              
              <div className="flex gap-2.5 justify-center shrink-0">
                {/* 🚀 TAKİP ET BUTONU */}
                <Button 
                    onClick={() => {
                        setIsFollowing(!isFollowing);
                        setFollowerCount(isFollowing ? followerCount - 1 : followerCount + 1);
                    }}
                    className={`rounded-full gap-2 px-8 transition-all duration-300 border-none ${
                        isFollowing 
                        ? "bg-slate-100 text-slate-500 hover:bg-slate-200 shadow-inner" 
                        : "bg-slate-900 hover:bg-teal-600 text-white shadow-xl hover:shadow-teal-500/10"
                    }`}
                >
                    {isFollowing ? (
                        <> <Check className="w-4 h-4" /> Takip Ediliyor </>
                    ) : (
                        <> <UserPlus className="w-4 h-4" /> Takip Et </>
                    )}
                </Button>

                {/* DÜZENLE VE İLETİŞİM */}
                <Button 
                  onClick={() => setIsEditModalOpen(true)}
                  variant="outline" 
                  size="icon" 
                  className="rounded-full border-slate-200 hover:bg-slate-50 hover:border-teal-100 hover:text-teal-700 shadow-sm transition-all"
                  title="Profili Düzenle"
                >
                  <Settings className="w-4 h-4" />
                </Button>
                
                <Button 
                  onClick={() => setIsContactModalOpen(true)}
                  variant="outline" 
                  size="icon" 
                  className="rounded-full border-slate-200 hover:bg-slate-50 hover:border-teal-100 hover:text-teal-700 shadow-sm transition-all"
                  title="İletişim Kur"
                >
                  <Mail className="w-4 h-4" />
                </Button>
              </div>
            </div>

            <p className="text-slate-500 dark:text-slate-400 text-lg leading-relaxed max-w-2xl italic font-light">
              "{userData.bio}"
            </p>

            <div className="flex flex-wrap justify-center md:justify-start gap-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-sm text-slate-400 font-medium">
              <div className="flex items-center gap-2">
                <Calendar className="w-4 h-4 text-teal-500" />
                <span>Mart 2026'dan beri üye</span>
              </div>
              <div className="flex items-center gap-2">
                <History className="w-4 h-4 text-teal-500" />
                <span>{mockPosts.length} Yayınlanmış Yazı</span>
              </div>
              <div className="flex items-center gap-2">
                <UserPlus className="w-4 h-4 text-teal-500" />
                <span className="text-slate-900 dark:text-white font-bold">{(followerCount / 1000).toFixed(1)}k</span> Takipçi
              </div>
              <div className="flex items-center gap-2">
                <Heart className="w-4 h-4 text-teal-500" />
                <span className="text-slate-900 dark:text-white font-bold">4.2k</span> Beğeni
              </div>
            </div>
          </div>
        </section>

        {/* TAB NAVIGATION */}
        <div className="flex gap-8 border-b border-slate-100 dark:border-slate-800 mb-10">
          <button 
            onClick={() => setActiveTab("posts")}
            className={`pb-4 text-sm font-bold tracking-widest uppercase transition-all relative ${activeTab === "posts" ? "text-slate-900 dark:text-white" : "text-slate-400 hover:text-slate-600"}`}
          >
            Yazılarım
            {activeTab === "posts" && <div className="absolute bottom-0 left-0 w-full h-0.5 bg-teal-500 animate-in zoom-in" />}
          </button>
        </div>

        {/* YAZI LİSTESİ */}
        <div className="grid gap-6 animate-in fade-in duration-700 delay-300">
          {mockPosts.map((post) => (
            <Link 
              key={post.id} 
              to={`/post/${post.slug}`} 
              className="group bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-6 rounded-[2.5rem] hover:shadow-2xl hover:shadow-teal-500/5 transition-all flex flex-col md:flex-row items-center gap-6 relative"
            >
              <div className="w-full md:w-52 h-36 rounded-3xl overflow-hidden shrink-0 shadow-inner border border-slate-100">
                <img src={post.coverImage} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
              </div>
              <div className="flex-1 space-y-3">
                <div className="flex items-center gap-3">
                    <Badge variant="secondary" className="bg-slate-100 text-slate-600 text-[10px] uppercase font-bold tracking-wider">{post.category}</Badge>
                    <span className="text-[10px] text-slate-400 flex items-center gap-1 uppercase tracking-tighter">
                        <History className="w-3 h-3" /> v2.4 Güncellendi
                    </span>
                </div>
                <h3 className="font-serif text-3xl italic text-slate-900 dark:text-white group-hover:text-teal-600 transition-colors leading-tight">
                  {post.title}
                </h3>
                <p className="text-slate-500 line-clamp-2 font-light text-sm">{post.excerpt}</p>
              </div>
              <div className="shrink-0 p-4 w-full md:w-auto flex justify-center">
                <div className="w-14 h-14 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition-all shadow-inner">
                  <ArrowUpRight className="w-6 h-6" />
                </div>
              </div>
            </Link>
          ))}
        </div>
      </main>

      {/* 🛠️ 1. PROFİL DÜZENLEME MODALI */}
      {isEditModalOpen && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4 animate-in fade-in duration-200">
          <div className="bg-white dark:bg-slate-900 w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
            <div className="p-8 border-b border-slate-50 dark:border-slate-800 flex justify-between items-center">
              <h2 className="font-serif text-3xl italic tracking-tighter text-slate-900 dark:text-white">Profili Düzenle</h2>
              <button onClick={() => setIsEditModalOpen(false)} className="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors"><X className="w-5 h-5" /></button>
            </div>
            
            <div className="p-8 space-y-6">
              <div className="flex flex-col items-center gap-4">
                <div className="relative group cursor-pointer">
                  <Avatar className="h-28 w-28 ring-4 ring-teal-50 dark:ring-teal-900/30">
                    <AvatarImage src={userData.avatar} />
                  </Avatar>
                  <div className="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <Camera className="w-6 h-6 text-white" />
                  </div>
                </div>
                <p className="text-[10px] font-bold uppercase tracking-widest text-slate-400">Görseli Değiştir</p>
              </div>

              <div className="space-y-5">
                <div className="space-y-1.5">
                  <label className="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-2">Görünen Ad</label>
                  <input 
                    type="text" 
                    value={userData.name}
                    onChange={(e) => setUserData({...userData, name: e.target.value})}
                    className="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3.5 focus:ring-2 ring-teal-500 outline-none transition-all dark:text-white"
                  />
                </div>
                <div className="space-y-1.5">
                  <label className="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-2">Biyografi</label>
                  <textarea 
                    value={userData.bio}
                    onChange={(e) => setUserData({...userData, bio: e.target.value})}
                    rows={4}
                    className="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3.5 focus:ring-2 ring-teal-500 outline-none transition-all resize-none dark:text-white"
                  />
                </div>
              </div>
            </div>

            <div className="p-8 bg-slate-50 dark:bg-slate-800/50 flex gap-3">
              <Button onClick={() => setIsEditModalOpen(false)} variant="ghost" className="flex-1 rounded-full hover:bg-slate-200">İptal</Button>
              <Button onClick={handleSave} className="flex-1 rounded-full bg-teal-600 hover:bg-teal-700 text-white gap-2 shadow-lg shadow-teal-500/20">
                <Check className="w-4 h-4" /> Kaydet
              </Button>
            </div>
          </div>
        </div>
      )}

      {/* 🛠️ 2. İLETİŞİM MODALI */}
      {isContactModalOpen && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4 animate-in fade-in duration-200">
          <div className="bg-white dark:bg-slate-900 w-full max-w-sm rounded-[3rem] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
            <div className="p-8 pb-4 flex justify-between items-center">
              <h2 className="font-serif text-3xl italic tracking-tighter text-slate-900 dark:text-white">Bağlantı Kur</h2>
              <button onClick={() => setIsContactModalOpen(false)} className="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors"><X className="w-5 h-5" /></button>
            </div>
            
            <div className="p-8 space-y-6">
              <p className="text-slate-500 dark:text-slate-400 text-sm font-light leading-relaxed italic">İş birlikleri ve teknik soruların için doğrudan e-posta gönderebilir veya sosyal ağlardan takibe alabilirsin.</p>
              
              <a href={`mailto:sarah@postify.com`} className="flex items-center gap-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl hover:bg-teal-50 dark:hover:bg-teal-900/30 hover:text-teal-700 transition-all group">
                <div className="w-12 h-12 bg-white dark:bg-slate-700 rounded-xl flex items-center justify-center shadow-sm group-hover:bg-teal-600 group-hover:text-white transition-all">
                  <Mail className="w-6 h-6" />
                </div>
                <div className="text-left">
                  <p className="text-[10px] font-bold uppercase tracking-tighter text-slate-400 leading-none mb-1">E-Posta</p>
                  <p className="font-medium dark:text-white">sarah@postify.com</p>
                </div>
              </a>

              <div className="grid grid-cols-3 gap-3">
                <a href="#" className="flex flex-col items-center gap-3 p-5 bg-slate-50 dark:bg-slate-800 rounded-3xl hover:bg-slate-900 hover:text-white dark:hover:bg-white dark:hover:text-slate-900 transition-all group">
                  <Github className="w-6 h-6" />
                  <span className="text-[10px] font-bold uppercase tracking-widest">Github</span>
                </a>
                <a href="#" className="flex flex-col items-center gap-3 p-5 bg-slate-50 dark:bg-slate-800 rounded-3xl hover:bg-sky-500 hover:text-white transition-all">
                  <Twitter className="w-6 h-6" />
                  <span className="text-[10px] font-bold uppercase tracking-widest">Twitter</span>
                </a>
                <a href="#" className="flex flex-col items-center gap-3 p-5 bg-slate-50 dark:bg-slate-800 rounded-3xl hover:bg-teal-600 hover:text-white transition-all">
                  <Globe className="w-6 h-6" />
                  <span className="text-[10px] font-bold uppercase tracking-widest">Web</span>
                </a>
              </div>
            </div>

            <div className="p-6 text-center text-[10px] text-slate-300 dark:text-slate-600 uppercase tracking-[0.2em] border-t border-slate-50 dark:border-slate-800 font-bold">
              Postify Elite Creator
            </div>
          </div>
        </div>
      )}

    </div>
  );
}